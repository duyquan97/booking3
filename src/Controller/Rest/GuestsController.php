<?php

namespace App\Controller\Rest;

use App\Entity\Guests;
use App\Entity\Prices;
use App\Entity\Rooms;
use App\Form\GuestType;
use App\Repository\GuestsRepository;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;
use Cocur\Slugify\Slugify;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\ErrorHandler\Error\FatalError;
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
        $guest = $this->guestsRepository->getGuests();
        if ($guest) {
            return View::create($this->guestsRepository->getGuests(), Response::HTTP_OK);
        }
        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);
    }

    /**
     * @Rest\Post("/guests", name="guests_new")
     *
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $guest = new Guests();
        $form = $this->createForm( GuestType::class, $guest);
        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid() ) {
            $guest->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($guest);
            $entityManager->flush();

            if ($guest) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }

        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\get("/guests/{id}", name="guests_show")
     */
    public function show(Guests $guest): View
    {
        return View::create($guest, Response::HTTP_OK);
    }


    /**
     * @Rest\Patch("/guests/{id}", name="guests_edit")
     */
    public function edit(Guests $guest, Request $request, ValidatorInterface $validator): View
    {
        $form = $this->createForm( GuestType::class, $guest);

        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $guest->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            if ($guest) {
                return View::create(['message' => 'update success'], Response::HTTP_OK);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/guests/{id}", name="guests_delete")
     */
    public function delete(Guests $guest ): View
    {
        if ($guest) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($guest);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);
    }
}
