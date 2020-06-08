<?php

namespace App\Controller\Rest;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\GuestsRepository;
use App\Repository\RoomsRepository;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class BookingController extends AbstractFOSRestController
{
    private $bookingsRepository;
    private $guestsRepository;
    private $roomsRepository;
    private $userRepository;
    private $stocksRepository;
    private $em;

    public function __construct(
        BookingRepository $bookingsRepository,
        GuestsRepository $guestsRepository,
        RoomsRepository $roomsRepository,
        UserRepository $userRepository,
        StocksRepository $stocksRepository,
        EntityManagerInterface $em
    ) {

        $this->bookingsRepository = $bookingsRepository;
        $this->guestsRepository = $guestsRepository;
        $this->roomsRepository = $roomsRepository;
        $this->userRepository = $userRepository;
        $this->stocksRepository = $stocksRepository;
        $this->em = $em;
    }

    /**
     * @Rest\Get("/bookings/", name="bookings_index")
     * @param  Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $bookings = $this->bookingsRepository->getBooking();

        return View::create($bookings, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/bookings", name="bookings_new")
     * @IsGranted("CREATE_BOOKING")
     * @param Request $request
     * @return View
     */
    public function new(Request $request): View
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_FORBIDDEN);
        }
        $fromDate = date_create($data['fromDate']);
        $toDate   = date_create($data['toDate']);
        $roomID   = $data['room'];
        $amount   = $data['amount'];
        $datediff = abs(strtotime($data['fromDate']) - strtotime($data['toDate']));
        $countDay   = floor($datediff / (60 * 60 * 24));
        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(
                ['error' => 'The start date must be greater than the end date!'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $listStocks = $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);
        if (!count($listStocks) == $countDay) {
            return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
        }
        $price = 0;
        foreach ($listStocks as $stock) {
            $price += $stock['price'];
            $stock = $this->stocksRepository->find($stock["id"]);
            $stock->setAmount($stock->getAmount() - $amount);
        }
        $booking->setPrice($price);
        $booking->setUser($user);
        $this->em->persist($booking);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\get("/bookings/{id}", name="bookings_show")
     * @IsGranted("BOOKING", subject="booking")
     * @param Booking $booking
     * @return View
     */
    public function show(Booking $booking): View
    {
        if ($booking) {
            return View::create($booking, Response::HTTP_OK);
        }

        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);
    }

    /**
     * @Rest\Patch("/bookings/{id}", name="bookings_edit")
     * @IsGranted("ADMIN")
     * @param Booking $booking
     * @param  Request $request
     * @return View
     */
    public function edit(Booking $booking, Request $request): View
    {
        $form = $this->createForm(BookingType::class, $booking);
        $user = $this->getUser();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_FORBIDDEN);
        }
        $fromDate = date_create($data['fromDate']);
        $toDate   = date_create($data['toDate']);
        $roomID   = $data['room'];
        $amount   = $data['amount'];
        $datediff = abs(strtotime($data['fromDate']) - strtotime($data['toDate']));
        $countDay   = floor($datediff / (60 * 60 * 24));
        if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
            return View::create(
                ['error' => 'The start date must be greater than the end date!'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $listStocks = $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);
        if (!count($listStocks) == $countDay) {
            return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
        }

        $price = 0;
        foreach ($listStocks as $stock) {
            $price += $stock['price'];
            $stock = $this->stocksRepository->find($stock["id"]);
            $stock->setAmount($stock->getAmount() - $amount);
        }
        $booking->setPrice($price);
        $booking->setUser($user);
        $this->em->flush();

        return View::create(['message' => 'create success'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/bookings/{id}", name="bookings_delete")
     * @IsGranted("BOOKING", subject="booking")
     * @param Booking $booking
     * @return View
     */
    public function delete(Booking $booking): View
    {
        if ($booking) {
            $this->em->remove($booking);
            $this->em->flush();
        }

        return View::create(['message' => 'delete success'], Response::HTTP_OK);
    }
}
