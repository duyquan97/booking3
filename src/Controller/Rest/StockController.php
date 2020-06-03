<?php

namespace App\Controller\Rest;

use App\Entity\Prices;
use App\Entity\Rooms;
use App\Entity\Stocks;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;
use App\Repository\StocksRepository;
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


class StockController extends AbstractFOSRestController
{
    private $stocksRepository;
    private $roomsRepository;
    public function __construct(StocksRepository $stocksRepository, RoomsRepository $roomsRepository)
    {
        $this->stocksRepository = $stocksRepository;
        $this->roomsRepository = $roomsRepository;
    }

    /**
     * @Rest\Get("/stocks/", name="stocks_index")
     */
    public function index(): View
    {
        return View::create($this->stocksRepository->findAll(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/stocks", name="stocks_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $stock =  new Stocks();
        $stock->setAmount($request->request->get('amount'));
        $stock->setFromDate(date_create($request->request->get('fromDate')));
        $stock->setToDate(date_create($request->request->get('toDate')));
        $stock->setRoom($this->roomsRepository->find($request->request->get('room')));

        $errors = $validator->validate($stock);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($stock);
        $entityManager->flush();

        if ($stock) {
            return View::create(['message' => 'create success'], Response::HTTP_CREATED);
        }
    }


    /**
     * @Rest\Patch("/stocks/{id}", name="stocks_edit")
     */
    public function edit(int $id, Request $request, ValidatorInterface $validator): View
    {
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $stock = $this->stocksRepository->find($id);
        $stock->setAmount($request->request->get('amount'));
        $stock->setFromDate(date_create($request->request->get('fromDate')));
        $stock->setToDate(date_create($request->request->get('toDate')));
        $stock->setRoom($this->roomsRepository->find($request->request->get('room')));

        $errors = $validator->validate($stock);
        if (count($errors) > 0) {
            return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        if ($stock) {
            return View::create(['message' => 'update success'], Response::HTTP_CREATED);
        }
    }

    /**
     * @Rest\Delete("/stocks/{id}", name="stocks_delete")
     */
    public function delete(int $id  ): View
    {
        $stock = $this->stocksRepository->find($id);
        if ($stock) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stock);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }

}
