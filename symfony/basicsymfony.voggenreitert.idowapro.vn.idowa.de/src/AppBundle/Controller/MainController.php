<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 04.04.2017
 * Time: 16:40
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction(){
        return $this->render('pages/homepage.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/profile", name="profile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction() {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('pages/profile.html.twig');
    }
}