<?php

namespace App\Controller\Rest;

use App\Entity\Guests;
use App\Form\GuestType;
use App\Repository\GuestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class GuestsController extends AbstractFOSRestController
{
    private $guestsRepository;
    private $em;

    public function __construct(GuestsRepository $guestsRepository, EntityManagerInterface $em)
    {
        $this->guestsRepository = $guestsRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/guests/", name="guests_index")
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $guest = $this->guestsRepository->getGuests();
        if ($guest) {
            return View::create($this->guestsRepository->getGuests(), Response::HTTP_OK);
        }

        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);
    }

    /**
     * @Rest\Post("/guests", name="guests_new")
     * @param Request $request
     * @return View
     *
     */
    public function new(Request $request): View
    {
        $guest = new Guests();
        $form = $this->createForm(GuestType::class, $guest);
        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        $guest->setUser($user);
        $this->em->persist($guest);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\get("/guests/{id}", name="guests_show")
     * @param Guests $guest
     * @return View
     */
    public function show(Guests $guest): View
    {
        return View::create($guest, Response::HTTP_OK);
    }

    /**
     * @Rest\Patch("/guests/{id}", name="guests_edit")
     * @param Guests $guest
     * @param Request $request
     * @return View
     */
    public function edit(Guests $guest, Request $request): View
    {
        $form = $this->createForm(GuestType::class, $guest);
        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        $guest->setUser($user);
        $this->em->flush();

        return View::create(['message' => 'update success'], Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/guests/{id}", name="guests_delete")
     * @param Guests $guest
     * @return View
     */
    public function delete(Guests $guest): View
    {
        if ($guest) {
            $this->em->remove($guest);
            $this->em->flush();
        }

        return View::create(['message' => 'delete success'], Response::HTTP_OK);
    }
}
