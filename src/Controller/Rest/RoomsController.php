<?php

namespace App\Controller\Rest;

use App\Entity\Rooms;
use App\Form\RoomsType;
use App\Repository\RoomsRepository;
use Cocur\Slugify\Slugify;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use Symfony\Component\HttpFoundation\File\Exception\IniSizeFileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RoomsController extends AbstractFOSRestController
{
    private $roomsRepository;
    public function __construct(RoomsRepository $roomsRepository)
    {
        $this->roomsRepository = $roomsRepository;
    }

    /**
     * @Rest\Get("/rooms/", name="rooms_index")
     * @param Request $request
     * @return View
     *
     */
    public function index(Request $request): View
    {
        $fromPrice = $request->query->get('fromPrice') ?? $fromPrice = 0;
        $toPrice = $request->query->get('toPrice') ?? $toPrice = 0;
        $fromDate = $request->query->get('fromDate') ?? $fromDate = '';
        $toDate = $request->query->get('toDate') ?? $toDate = '';
        $data = $this->roomsRepository->findBySearch( $fromPrice, $toPrice, $fromDate, $toDate);

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/rooms", name="rooms_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $slugify = new Slugify();
        $room =  new Rooms();
        $form = $this->createForm( RoomsType::class, $room);

        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $room->setSlug($slugify->slugify(strtoupper(uniqid()).''.$request->request->get('name')));

            $errors = $validator->validate($room);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            if ($room) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/rooms/{id}", name="rooms_show")
     */
    public function show(int $id): View
    {
        return View::create($this->roomsRepository->findById($id), Response::HTTP_OK);
    }

    /**
     * @Rest\Patch("/rooms/{id}", name="rooms_edit")\
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Rooms $room, Request $request, ValidatorInterface $validator): View
    {
        $slugify = new Slugify();
        $form = $this->createForm( RoomsType::class, $room);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $room->setSlug($slugify->slugify(strtoupper(uniqid()).''.$request->request->get('name')));

            $errors = $validator->validate($room);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            if ($room) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/rooms/{id}", name="rooms_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Rooms $room): View
    {
        if ($room) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }
}
