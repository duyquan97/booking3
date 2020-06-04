<?php
namespace App\Form\DataTransformer;
use App\Entity\User;

use App\Repository\GuestsRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Session\Session;

class GuestTransformer implements DataTransformerInterface
{
    private $guestsRepository;
    public function __construct(GuestsRepository $guestsRepository)
    {
        $this->guestsRepository = $guestsRepository;
    }
    public function transform($room)
    {
        if (null === $room) {
            return '';
        }
        return $room->getId();
    }
    public function reverseTransform($roomId)
    {
        if (!$roomId) {
            return;
        }
        $guest = $this->guestsRepository->find($roomId);
        if (null === $guest) {
            $privateErrorMessage = sprintf('An guest with number "%s" does not exist!', $roomId);
            $publicErrorMessage = 'The given "{{ value }}" value is not a valid room number.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $roomId,
            ]);

            throw $failure;
        }
        return $guest;

    }
}