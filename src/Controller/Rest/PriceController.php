<?php

namespace App\Controller\Rest;

use App\Entity\Prices;
use App\Form\PriceType;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class PriceController extends AbstractFOSRestController
{
    private $pricesRepository;
    private $roomsRepository;
    private $em;

    public function __construct(
        PricesRepository $pricesRepository,
        RoomsRepository $roomsRepository,
        EntityManagerInterface $em
    ) {
        $this->pricesRepository = $pricesRepository;
        $this->roomsRepository = $roomsRepository;
        $this->em = $em;
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
     * @param Request $request
     * @return View
     */
    public function new(Request $request): View
    {
        $price = new Prices();
        $form = $this->createForm(PriceType::class, $price);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }

        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(
                ['error' => 'The start date must be greater than the end date!'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $this->em->persist($price);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch("/prices/{id}", name="prices_edit")
     * @param Prices $price
     * @param Request
     * @return View
     */
    public function edit(Prices $price, Request $request): View
    {
        $form = $this->createForm(PriceType::class, $price);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(
                ['error' => 'The start date must be greater than the end date!'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $this->em->flush();

        return View::create(['message' => 'update success'], Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/prices/{id}", name="prices_delete")
     * @param Prices $price
     * @return View
     */
    public function delete(Prices $price): View
    {
        if ($price) {
            $this->em->remove($price);
            $this->em->flush();
        }

        return View::create(['message' => 'delete success'], Response::HTTP_OK);
    }
}
