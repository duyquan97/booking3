<?php

namespace App\Controller\Rest;

use App\Entity\Stocks;
use App\Form\StockType;
use App\Repository\RoomsRepository;
use App\Repository\StocksRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class StockController extends AbstractFOSRestController
{
    private $stocksRepository;
    private $roomsRepository;
    private $em;

    public function __construct(
        StocksRepository $stocksRepository,
        RoomsRepository $roomsRepository,
        EntityManagerInterface $em
    ) {
        $this->stocksRepository = $stocksRepository;
        $this->roomsRepository = $roomsRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/stocks/", name="stocks_index")
     * @return View
     */
    public function index(): View
    {
        $room = $this->stocksRepository->findAll();

        return View::create($room, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/stocks", name="stocks_new")
     * @param Request $request
     * @return View
     */
    public function new(Request $request): View
    {
        $stocks = new Stocks();
        $form = $this->createForm(StockType::class, $stocks);
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
        $this->em->persist($stocks);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch("/stocks/{id}", name="stocks_edit")
     * @param Stocks $stocks
     * @param Request $request
     * @return  View
     */
    public function edit(Stocks $stocks, Request $request): View
    {
        $form = $this->createForm(StockType::class, $stocks);
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

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/stocks/{id}", name="stocks_delete")
     * @param Stocks $stock
     * @return View
     */
    public function delete(Stocks $stock): View
    {
        if ($stock) {
            $this->em->remove($stock);
            $this->em->flush();
        }

        return View::create(['message' => 'delete success'], Response::HTTP_OK);
    }
}
