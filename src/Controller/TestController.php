<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/", name="test")
     */
    public function index(LoggerInterface $logger)
    {
        $logger->info('Inside the controller');
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
