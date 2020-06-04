<?php

namespace App\Controller\Rest;


use App\Entity\Rooms;
use App\Entity\Stocks;
use App\Form\PriceType;

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
        $stocks = new Stocks();
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $form = $this->createForm( PriceType::class, $stocks);
        $form->submit($data);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($stocks);
        $entityManager->flush();

        if ($stocks) {
            return View::create(['message' => 'create success'], Response::HTTP_CREATED);
        }
    }


    /**
     * @Rest\Patch("/stocks/{id}", name="stocks_edit")
     */
    public function edit(Stocks $stocks, Request $request, ValidatorInterface $validator): View
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        $form = $this->createForm( PriceType::class, $stocks);
        $form->submit($data);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        if ($stocks) {
            return View::create(['message' => 'update success'], Response::HTTP_CREATED);
        }
    }

    /**
     * @Rest\Delete("/stocks/{id}", name="stocks_delete")
     */
    public function delete(Stocks $stock  ): View
    {
        if ($stock) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stock);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }

}
