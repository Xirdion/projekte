<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 25.04.2017
 * Time: 16:28
 */

namespace AppBundle\Controller\Api;


use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends BaseController
{
    /**
     * @Route("/api/tokens")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function newTokenAction(Request $request) {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->getPassword());

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder.default')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 36000 // 1 hour expiration
            ]);

        return new JsonResponse(['token' => $token]);
    }
}