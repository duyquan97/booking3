<?php
namespace App\Controller\Rest;

use App\Entity\User;
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
         $body = $request->getContent();
         $data = json_decode($body,true);
         $email = $data['email'];
         $password = $data['password'];
         $role = $data['role'];
             $user = new User();
             $encodedPassword = $passwordEncoder->encodePassword($user, $password);
             $user->setEmail($email);
             $user->setRoles($role);
             $user->setPassword($encodedPassword);

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($user);
             $entityManager->flush();

         return View::create(['message' => 'create user success'], Response::HTTP_OK);
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