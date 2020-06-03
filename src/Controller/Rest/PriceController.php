<?php

namespace App\Controller\Rest;

use App\Entity\Prices;
use App\Entity\Rooms;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;
use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\File\Exception\FormSizeFileException;
use Symfony\Component\HttpFoundation\File\Exception\IniSizeFileException;
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
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $price =  new Prices();
        $price->setPrice($request->request->get('price'));
        $price->setFromDate(date_create($request->request->get('fromDate')));
        $price->setToDate(date_create($request->request->get('toDate')));
        $price->setRoom($this->roomsRepository->find($request->request->get('room')));

        $errors = $validator->validate($price);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($price);
        $entityManager->flush();

        if ($price) {
            return View::create(['message' => 'create success'], Response::HTTP_CREATED);
        }
    }

    /**
     * @Rest\Patch("/prices/{id}", name="prices_edit")
     */
    public function edit(int $id, Request $request, ValidatorInterface $validator): View
    {
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $price = $this->pricesRepository->find($id);
        $price->setPrice($request->request->get('price'));
        $price->setFromDate(date_create($request->request->get('fromDate')));
        $price->setToDate(date_create($request->request->get('toDate')));
        $price->setRoom($this->roomsRepository->find($request->request->get('room')));

        $errors = $validator->validate($price);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        if ($price) {
            return View::create(['message' => 'update success'], Response::HTTP_CREATED);
        }
    }

    /**
     * @Rest\Delete("/prices/{id}", name="prices_delete")
     */
    public function delete(int $id  ): View
    {
        $price = $this->pricesRepository->find($id);
        if ($price) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($price);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }
}
