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
    public function transform($guest)
    {
        if (null === $guest) {
            return '';
        }
        return $guest->getId();
    }
    public function reverseTransform($guestId)
    {
        if (!$guestId) {
            return;
        }
        $guest = $this->guestsRepository->find($guestId);
        if (null === $guest) {
            $privateErrorMessage = sprintf('An guest with number "%s" does not exist!', $guestId);
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