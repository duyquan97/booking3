<?php

namespace App\Controller\Rest;


use App\Entity\Rooms;
use App\Entity\Stocks;
use App\Form\PriceType;

use App\Form\StockType;
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
        $room = $this->stocksRepository->findAll();
        if ($room) {
            return View::create( $room, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Post("/stocks", name="stocks_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $stocks = new Stocks();
        $form = $this->createForm( StockType::class, $stocks);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
                return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
            }
            $errors = $validator->validate($stocks);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stocks);
            $entityManager->flush();
            if ($stocks) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }


    /**
     * @Rest\Patch("/stocks/{id}", name="stocks_edit")
     */
    public function edit(Stocks $stocks, Request $request, ValidatorInterface $validator): View
    {
        $form = $this->createForm( StockType::class, $stocks);
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
                return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
            }
            $errors = $validator->validate($stocks);
            if (count($errors) > 0) {
                return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            if ($stocks) {
                return View::create(['message' => 'create success'], Response::HTTP_CREATED);
            }
        }
        return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/stocks/{id}", name="stocks_delete")
     */
    public function delete(Stocks $stock  ): View
    {
        try {
            if ($stock) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($stock);
                $entityManager->flush();
                return View::create(['message' => 'delete success'], Response::HTTP_OK);
            }
        }
        catch (\Exception $exception) {
            return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);
        }


    }

}
