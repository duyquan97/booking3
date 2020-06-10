<?php

namespace App\Controller\Rest;

use App\Entity\Rooms;
use App\Form\RoomsType;
use App\Repository\RoomsRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class RoomsController extends AbstractFOSRestController
{
    private $roomsRepository;
    private $em;

    public function __construct(
        RoomsRepository $roomsRepository,
        EntityManagerInterface $em
    ) {
        $this->roomsRepository = $roomsRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/rooms/", name="rooms_index")
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $fromPrice = $request->query->get('fromPrice') ?? $fromPrice = 0;
        $toPrice = $request->query->get('toPrice') ?? $toPrice = 0;
        $fromDate = $request->query->get('fromDate') ?? $fromDate = '';
        $toDate = $request->query->get('toDate') ?? $toDate = '';
        $data = $this->roomsRepository->findBySearch($fromPrice, $toPrice, $fromDate, $toDate);

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/rooms", name="rooms_new")
     * @IsGranted("ROLE_ADMIN")
     * @param Request$request
     * @return View
     */
    public function new(Request $request): View
    {

        $room =  new Rooms();
        $form = $this->createForm(RoomsType::class, $room);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        $room->setSlug(strtoupper(uniqid()).''.str_replace(' ', $data['name']));
        $this->em->persist($room);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/rooms/{id}", name="rooms_show")
     * @param Int $id
     * @return View
     */
    public function show(int $id): View
    {
        return View::create($this->roomsRepository->findById($id), Response::HTTP_OK);
    }

    /**
     * @Rest\Patch("/rooms/{id}", name="rooms_edit")\
     * @IsGranted("ROLE_ADMIN")
     * @param Rooms $room
     * @param Request
     * @return View
     */
    public function edit(Rooms $room, Request $request): View
    {

        $form = $this->createForm(RoomsType::class, $room);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        $room->setSlug(strtoupper(uniqid()).''.str_replace(' ', $data['name']));
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/rooms/{id}", name="rooms_delete")
     * @IsGranted("ROLE_ADMIN")
     * @param Rooms $room
     * @return View
     */
    public function delete(Rooms $room): View
    {
        if ($room) {
            $this->em->remove($room);
            $this->em->flush();
        }

        return View::create(['message' => 'delete success'], Response::HTTP_OK);
    }
}
