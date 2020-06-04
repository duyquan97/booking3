<?php
namespace App\Controller\Rest;

use App\Entity\Prices;
use App\Entity\User;
use App\Form\PriceType;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestController extends AbstractController
 {
    /**
     * @Route("/prices2", name="prices_new2", methods={"POST"})
     */
    public function new(Request $request)
    {
        $price = new Prices();
        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm( PriceType::class, $price);
        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($price);

        $entityManager->flush();

    }
 }