<?php

namespace App\Controller\Rest;

use App\Entity\Prices;
use App\Entity\Rooms;
use App\Form\PriceType;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class PriceController extends AbstractFOSRestController
{
    private $pricesRepository;
    private $roomsRepository;
    public function __construct(PricesRepository $pricesRepository, RoomsRepository $roomsRepository)
    {
        $this->pricesRepository = $pricesRepository;
        $this->roomsRepository = $roomsRepository;
    }

    /**
     * @Rest\Get("/prices/", name="prices_index")
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return View::create($this->pricesRepository->findAll(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/prices", name="prices_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $price = new Prices();
        $form = $this->createForm( PriceType::class, $price);

        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
                return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
            }

            $errors = $validator->validate($price);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($price);
            $entityManager->flush();

            if ($price) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Patch("/prices/{id}", name="prices_edit")
     */
    public function edit(Prices $price, Request $request, ValidatorInterface $validator): View
    {
        $form = $this->createForm( PriceType::class, $price);

        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
                return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
            }
            $errors = $validator->validate($price);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            if ($price) {
                return View::create(['message' => 'update success'], Response::HTTP_OK);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/prices/{id}", name="prices_delete")
     */
    public function delete(Prices $price  ): View
    {
        if ($price) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($price);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }
}
