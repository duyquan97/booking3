<?php

namespace App\Controller\Rest;

use App\Entity\Rooms;
use App\Repository\RoomsRepository;
use Cocur\Slugify\Slugify;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
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
     */
    public function index(Request $request): View
    {
        $fromPrice = $request->query->get('fromPrice');
        $toPrice = $request->query->get('toPrice');
        $fromDate = $request->query->get('fromDate');
        $toDate = $request->query->get('toDate');
        $data = $this->roomsRepository->findBySearch($fromPrice, $toPrice, $fromDate, $toDate);

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/rooms", name="rooms_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {

        $slugify = new Slugify();
        $room =  new Rooms();
        $room->setName($request->request->get('name'));
        $room->setSlug($slugify->slugify(strtoupper(uniqid()).''.$request->request->get('name')));
        $room->setShortDescription($request->request->get('short_description'));
        $room->setDescription($request->request->get('description'));
        $room->setPerson($request->request->get('person'));
        $room->setProvince($request->request->get('province'));
        $room->setDistrict($request->request->get('district'));
        $room->setStreet($request->request->get('street'));
        $room->setStatus($request->request->get('status') ?? 1);
        $room->setFeatured($request->request->get('featured') ?? 0);
        $room->setType($request->request->get('type') ?? 1);

        $errors = $validator->validate($room);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($room);
        $entityManager->flush();

        if ($room) {
            return View::create(['message' => 'create success'], Response::HTTP_CREATED);
        }

    }

    /**
     * @Rest\Get("/rooms/{id}", name="rooms_show")
     */
    public function show(int $id): View
    {
        return View::create($this->roomsRepository->findById($id), Response::HTTP_OK);
    }

    /**
     * @Rest\Patch("/rooms/{id}", name="rooms_edit")
     */
    public function edit(int $id, Request $request, ValidatorInterface $validator): View
    {
        $slugify = new Slugify();
        $room = $this->roomsRepository->find($id);
        $room->setName($request->request->get('name'));
        $room->setSlug($slugify->slugify(strtoupper(uniqid()).''.$request->request->get('name')));
        $room->setShortDescription($request->request->get('short_description'));
        $room->setDescription($request->request->get('description'));
        $room->setPerson($request->request->get('person'));
        $room->setProvince($request->request->get('province'));
        $room->setDistrict($request->request->get('district'));
        $room->setStreet($request->request->get('street'));
        $room->setStatus($request->request->get('status') ?? 1);
        $room->setFeatured($request->request->get('featured') ?? 0);
        $room->setType($request->request->get('type') ?? 1);

        $errors = $validator->validate($room);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return View::create(['message' => 'update success'], Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/rooms/{id}", name="rooms_delete")
     */
    public function delete(int $id  ): View
    {
        $room = $this->roomsRepository->find($id);
        if ($room) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }

}
