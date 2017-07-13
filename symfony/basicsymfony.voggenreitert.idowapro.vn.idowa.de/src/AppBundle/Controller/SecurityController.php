<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 05.04.2017
 * Time: 16:16
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;
use AppBundle\Form\RegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(){
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername
        ]);

        return $this->render('user/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error
            )
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){
        throw new \Exception("This should not be reached!");
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request){
        $form = $this->createForm(RegistrationForm::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome '.$user->getUsername());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user, $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}