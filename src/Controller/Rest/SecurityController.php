<?php
namespace App\Controller\Rest;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\RoomsType;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

 class SecurityController extends AbstractController
 {
     /**
      * @Rest\Post("/register", name="api_register")
      *
      */
     public function register(UserPasswordEncoderInterface $passwordEncoder, Request $request)
     {
         $user = new User();
         $body = $request->getContent();
         $data = json_decode($body,true);

         $form = $this->createForm( RegisterType::class, $user);

         $form->submit($data);

         if ($form->isSubmitted() && $form->isValid()) {
             $encodedPassword = $passwordEncoder->encodePassword($user, $data['password']);
             $user->setPassword($encodedPassword);
             $user->setRoles(['ROLE_USER']);
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($user);
             $entityManager->flush();
             if ($user) {
                 return View::create(['message' => 'create user success'], Response::HTTP_OK);
             }
         }
         return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
     }

     /**
      * @Rest\Post("/login", name="api_login")
      */
     public function login()
     {
         return $this->json(['result' => true]);

     }

     /**
      * @Rest\Post("/logout", name="api_logout")
      */
     public function logout()
     {
         return $this->json(['result' => true]);

     }
 }