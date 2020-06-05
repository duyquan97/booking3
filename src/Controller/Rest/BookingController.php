<?php

namespace App\Controller\Rest;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\GuestsRepository;
use App\Repository\RoomsRepository;
use App\Repository\StocksRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $bookings = $this->bookingsRepository->getBooking();
        if ($bookings) {
            return View::create( $bookings, Response::HTTP_OK);
        }
        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);

    }

    /**
     * @Rest\Post("/bookings", name="bookings_new")
     * @IsGranted("CREATE_BOOKING")
     */
    public function new(Request $request, ValidatorInterface $validator, GuestsRepository $guestsRepository): View
    {
        $booking = new Booking();
        $form = $this->createForm( BookingType::class, $booking);
        try {
            $user = $this->getUser();
            $body = $request->getContent();
            $data = json_decode($body, true);
            $form->submit($data);
            $fromDate = date_create($data['fromDate']);
            $toDate   = date_create($data['toDate']);
            $roomID   = $data['room'];
            $amount   = $data['amount'];

            if (strtotime($data['fromDate']) >= strtotime($data['toDate']) || strtotime($data['fromDate']) < strtotime('now')) {
                return View::create(['error' => 'Invalid date!'], Response::HTTP_BAD_REQUEST);
            }

            $datediff = abs(strtotime($data['fromDate']) - strtotime($data['toDate']));
            $countDay   = floor($datediff / (60 * 60 * 24));
            $listStocks = $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);

            if (count($listStocks) == $countDay) {
                $price = 0;
                foreach ($listStocks as $stock) {
                    $price += $stock['price'];
                    $stock = $this->stocksRepository->find($stock["id"]);
                    $stock->setAmount($stock->getAmount() - $amount);
                }

                $booking->setPrice($price);
                $booking->setUser($user);

                $errors = $validator->validate($booking);
                if (count($errors) > 0) {
                    return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($booking);
                $entityManager->flush();

                if ($booking) {
                    return View::create(['message' => 'create success'], Response::HTTP_CREATED);
                }
            }
            return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
        }
        catch (\Exception $exception) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Rest\get("/bookings/{id}", name="bookings_show")
     * @IsGranted("BOOKING", subject="booking")
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
     */
    public function edit(Booking $booking, Request $request, ValidatorInterface $validator): View
    {
        $form = $this->createForm( BookingType::class, $booking);
        try {
            $user = $this->getUser();
            $body = $request->getContent();
            $data = json_decode($body, true);
            $fromDate = date_create($data['fromDate']);
            $toDate   = date_create($data['toDate']);
            $roomID   = $data['room'];
            $amount   = $data['amount'];

            $datediff = abs(strtotime($data['fromDate']) - strtotime($data['toDate']));
            $countDay   = floor($datediff / (60 * 60 * 24));
            if (strtotime($data['fromDate']) >= strtotime($data['toDate'])) {
                return View::create(['error' => 'The start date must be greater than the end date!'], Response::HTTP_BAD_REQUEST);
            }

            $listStocks = $this->roomsRepository->checkStock($roomID, $fromDate, $toDate, $amount);
            if (count($listStocks) == $countDay) {
                $price = 0;
                foreach ($listStocks as $stock) {
                    $price += $stock['price'];
                    $stock = $this->stocksRepository->find($stock["id"]);
                    $stock->setAmount($stock->getAmount() - $amount);
                }

                $form->submit($data);
                $booking->setPrice($price);
                $booking->setUser($user);


                $errors = $validator->validate($booking);
                if (count($errors) > 0) {
                    return View::create(['error' => $errors->get(1)->getMessage()], Response::HTTP_BAD_REQUEST);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                if ($booking) {
                    return View::create(['message' => 'create success'], Response::HTTP_CREATED);
                }
            } else {
                return View::create(['error' => 'The date you selected is not enough rooms!'], Response::HTTP_BAD_REQUEST);
            }
        }
        catch (\Exception $exception) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Rest\Delete("/bookings/{id}", name="bookings_delete")
     * @IsGranted("BOOKING", subject="booking")
     */
    public function delete(Booking $booking ): View
    {
        if ($booking) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
            return View::create(['message' => 'delete success'], Response::HTTP_OK);
        }
        return View::create(['error' => 'no data'], Response::HTTP_FORBIDDEN);

    }
}
