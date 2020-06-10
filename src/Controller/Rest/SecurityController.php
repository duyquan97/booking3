<?php
namespace App\Controller\Rest;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class SecurityController extends AbstractFOSRestController
{
    private $passwordEncoder;

    private function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
      * @Rest\Post("/register", name="api_register")
      * @param Request $request
      * @param EntityManagerInterface $em
      * @return View
      */
    public function register(Request $request, EntityManagerInterface $em)
    {

        $user = new User();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $form = $this->createForm(RegisterType::class, $user);
        $form->submit($data);
        if (!$form->isValid()) {
            return View::create(['error' => $form->getErrors()->getForm()], Response::HTTP_BAD_REQUEST);
        }
        $encodedPassword = $$this->passwordEncoder->encodePassword($user, $data['password']);
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();

        return View::create(['message' => 'create user success'], Response::HTTP_OK);
    }

    /**
    * @Rest\Post("/login", name="api_login")
    */
    public function login()
    {
    }

    /**
    * @Rest\Post("/logout", name="api_logout")
    */
    public function logout()
    {
    }
}
