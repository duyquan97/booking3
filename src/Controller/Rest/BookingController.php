<?php

namespace App\Controller\Rest;

use App\Entity\Booking;
use App\Entity\Guests;
use App\Entity\Prices;
use App\Entity\Rooms;
use App\Repository\BookingRepository;
use App\Repository\GuestsRepository;
use App\Repository\PricesRepository;
use App\Repository\RoomsRepository;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
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


class BookingController extends AbstractFOSRestController
{
    private $bookingsRepository;
    private $guestsRepository;
    private $roomsRepository;
    private $userRepository;
    private $stocksRepository;
    public function __construct(BookingRepository $bookingsRepository, GuestsRepository $guestsRepository,
           RoomsRepository $roomsRepository, UserRepository $userRepository, StocksRepository $stocksRepository)
    {
        $this->bookingsRepository = $bookingsRepository;
        $this->guestsRepository = $guestsRepository;
        $this->roomsRepository = $roomsRepository;
        $this->userRepository = $userRepository;
        $this->stocksRepository = $stocksRepository;
    }

    /**
     * @Rest\Get("/bookings/", name="bookings_index")
     * @param Request $request
     */
    public function index(Request $request): View
    {
        return View::create($this->bookingsRepository->getListBooking(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/bookings", name="bookings_new")
     */
    public function new(Request $request, ValidatorInterface $validator): View
    {
        $fromDate = date_create(Carbon::parse($request->request->get('fromDate'))->toDateString());
        $toDate   = date_create(Carbon::parse($request->request->get('toDate'))->toDateString());
        $roomID   = $request->request->get('roomId');
        $amount   = $request->request->get('amount');

        $datediff = abs(strtotime($request->request->get('fromDate')) - strtotime($request->request->get('toDate')));
        $countDay   = floor($datediff / (60 * 60 * 24));
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        else if (!empty($fromDate) && !empty($toDate) && !empty($roomID) && !empty($amount)) {
            $listStocks =  $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);
            if (count($listStocks) == $countDay) {
                $price = 0;
                foreach ($listStocks as $stock) {
                    $price += $stock['price'];
                    $stock = $this->stocksRepository->find($stock["id"]);
                    $stock->setAmount($stock->getAmount() - $amount);
                }
                $booking =  new Booking();
                $booking->setGuest($this->guestsRepository->find($request->request->get('guestId')));
                $booking->setRoom($this->roomsRepository->find($request->request->get('roomId')));
//                $booking->setUser($this-$this->userRepository->find($request->request->get('user')));
                $booking->setAmount($request->request->get('amount'));
                $booking->setPrice($price);
                $booking->setFromDate(date_create($request->request->get('fromDate')));
                $booking->setToDate(date_create($request->request->get('toDate')));
                $booking->setCode('BK'.strtoupper(uniqid()));
                $errors = $validator->validate($booking);
                if (count($errors) > 0) {
                    return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
                }
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($booking);


                $entityManager->flush();


                if ($booking) {
                    return View::create(['message' => 'create success'], Response::HTTP_CREATED);
                }
            }
            else {
                return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
            }
        }

    }

    /**
     * @Rest\get("/bookings/{id}", name="bookings_show")
     */
    public function show(int $id): View
    {
        return View::create($this->bookingsRepository->find($id), Response::HTTP_OK);
    }


    /**
     * @Rest\Patch("/bookings/{id}", name="bookings_edit")
     */
    public function edit(int $id, Request $request, ValidatorInterface $validator): View
    {

        $fromDate = date_create(Carbon::parse($request->request->get('fromDate'))->toDateString());
        $toDate   = date_create(Carbon::parse($request->request->get('toDate'))->toDateString());
        $roomID   = $request->request->get('roomId');
        $amount   = $request->request->get('amount');

        $datediff = abs(strtotime($request->request->get('fromDate')) - strtotime($request->request->get('toDate')));
        $countDay   = floor($datediff / (60 * 60 * 24));
        if (strtotime(Carbon::parse($request->request->get('fromDate'))->toDateString()) >= strtotime(Carbon::parse($request->request->get('toDate'))->toDateString())) {
            return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
        }
        else if (!empty($fromDate) && !empty($toDate) && !empty($roomID) && !empty($amount)) {
            $listStocks =  $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);
            if (count($listStocks) == $countDay) {
                $price = 0;
                foreach ($listStocks as $stock) {
                    $price += $stock['price'];
                }
                $booking =  $this->bookingsRepository->find($id);
                $booking->setGuest($this->guestsRepository->find($request->request->get('guestId')));
                $booking->setRoom($this->roomsRepository->find($request->request->get('roomId')));
//                $booking->setUser($this-$this->userRepository->find($request->request->get('user')));
                $booking->setAmount($request->request->get('amount'));
                $booking->setPrice($price);
                $booking->setFromDate(date_create($request->request->get('fromDate')));
                $booking->setToDate(date_create($request->request->get('toDate')));
                $booking->setCode('BK'.strtoupper(uniqid()));
                $errors = $validator->validate($booking);
                if (count($errors) > 0) {
                    return View::create(['error' => $errors], Response::HTTP_BAD_REQUEST);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                if ($booking) {
                    return View::create(['message' => 'update success'], Response::HTTP_CREATED);
                }
            }
            else {
                return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * @Rest\Delete("/bookings/{id}", name="bookings_delete")
     */
    public function delete(int $id  ): View
    {
        $booking = $this->bookingsRepository->find($id);
        if ($booking) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
    }
}
