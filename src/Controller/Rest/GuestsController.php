<?php

namespace App\Controller\Rest;

use App\Entity\Guests;
use App\Entity\Prices;
use App\Entity\Rooms;
use App\Repository\GuestsRepository;
use App\Repository\PricesRepository;
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


class GuestsController extends AbstractFOSRestController
{
    private $guestsRepository;
    public function __construct(GuestsRepository $guestsRepository)
    {
        $this->guestsRepository = $guestsRepository;
    }

    /**
     * @Rest\Get("/guests/", name="guests_index")
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return View::create($this->guestsRepository->findAll(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/guests", name="guests_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $guest =  new Guests();
        $guest->setName($request->request->get('name'));
        $guest->setPhone($request->request->get('phone'));
        $guest->setEmail($request->request->get('email'));

        $errors = $validator->validate($guest);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($guest);
        $entityManager->flush();

        if ($guest) {
            return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
    }

    /**
     * @Rest\get("/guests/{id}", name="guests_show")
     */
    public function show(int $id): View
    {
        return View::create($this->guestsRepository->find($id), Response::HTTP_OK);
    }


    /**
     * @Rest\Patch("/guests/{id}", name="guests_edit")
     */
    public function edit(int $id, Request $request, ValidatorInterface $validator): View
    {
        $guest = $this->guestsRepository->find($id);
        $guest->setName($request->request->get('name'));
        $guest->setEmail($request->request->get('email'));
        $guest->setPhone($request->request->get('phone'));

        $errors = $validator->validate($guest);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        if ($guest) {
            return View::create(['message' => 'update success'], Response::HTTP_CREATED);
        }
    }

    /**
     * @Rest\Delete("/guests/{id}", name="guests_delete")
     */
    public function delete(int $id  ): View
    {
        $guest = $this->guestsRepository->find($id);
        if ($guest) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($guest);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }
}
