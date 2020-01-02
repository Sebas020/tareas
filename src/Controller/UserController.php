<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    
    public function register(Request $request,UserPasswordEncoderInterface $encoder)
    {
    	//Crear el formulario
    	$user = new User();
    	$form = $this->createForm(RegisterType::class, $user);
    	//Rellenar el objeto con los datos del form
    	$form->handleRequest($request);//Unir lo que envia la request con el objeto que tiene vinculado el formulario (user)
    	//Comprobar si el form se ha enviado
    	if($form->isSubmitted() && $form->isValid()) {//Validar formulario
    		// Modificar el objeto para guardarlo
    		$user->setRole('ROLE_USER');
    		$date_now = new \DateTime('now');
    		$user->setCreateAt($date_now);
    		//Cifrando la contraseña
    		$encoded = $encoder->encodePassword($user, $user->getPassword());
    		$user->setPassword($encoded);

    		//Guardar usuario
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($user);
    		$em->flush();

    		return $this->redirectToRoute('tasks');
    	}

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function login(AuthenticationUtils $autenticationUtils) {
    	$error = $autenticationUtils->getLastAuthenticationError();

    	$lastUsername = $autenticationUtils->getLastUsername();

    	return $this->render('user/login.html.twig', array(
    		'error' => $error,
    		'last_username' => $lastUsername
    	));
    }
}
