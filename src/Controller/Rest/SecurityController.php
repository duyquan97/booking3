<?php
namespace App\Controller\Rest;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

 class SecurityController extends AbstractController
 {
     /**
      * @Rest\Post("/register", name="api_register")
      */
     public function register(UserPasswordEncoderInterface $passwordEncoder, Request $request)
     {
         $email = $request->request->get("email");
         $password = $request->request->get("password");
         $roles = explode(',', $request->request->get("role"));
         if ($password != $password) {
             $errors[] = "Password does not match the password confirmation.";
         }
         if (strlen($password) < 6) {
             $errors[] = "Password should be at least 6 characters.";
         }

         if (!$errors) {
             $user = new User();
             $encodedPassword = $passwordEncoder->encodePassword($user, $password);
             $user->setEmail($email);
             $user->setRoles($roles);
             $user->setPassword($encodedPassword);

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($user);
             $entityManager->flush();
         }

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