<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 10:48
 */

namespace AppBundle\Controller\Api;


use AppBundle\Controller\BaseController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class SecurityController
 * @package AppBundle\Controller\Api
 */
class SecurityController extends BaseController
{
    /**
     * Check if the user is exists and the password is valid.
     * Then return the JSON-Web-Token created with the username
     *
     * @ApiDoc(
     *     section="Security",
     *     description="Login-Method",
     *     headers={
     *          { "name"="PHP-AUTH-USER", "description"="username", "required"="true" },
     *          { "name"="PHP-AUTH-PW",   "description"="password", "required"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("api/login", name="api_login")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        // 1. check if this user exists

        /** @var User $user */
        $user = $this->getUserRepository()
            ->findOneBy(['username' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // 2. check if the password is correct

        /** @var bool $isValid */
        $isValid = $this->get('security.password_encoder')->isPasswordValid($user, $request->getPassword());
        if (!$isValid) {
            throw new BadCredentialsException();
        }

        // 3. create JWT-Token
        $token = $this->getUserToken($user);

        return new JsonResponse(['token' => $token], 200);
    }

    /**
     * Try to create a new user and save it to the DB.
     * Then return the JSON-Web-Token created with the username.
     *
     * @ApiDoc(
     *     section="Security",
     *     description="Register-Method",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="unique Username" },
     *          { "name"="email",    "dataType"="string", "description"="email",    "requirement"="email address" },
     *          { "name"="password", "dataType"="string", "description"="password", "requirement"="password" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/register", name="api_register")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        /** @var User $user */
        $user = new User();

        // 1. fill user with request data

        $form = $this->createForm(UserType::class, $user);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            throw $this->throwApiProblemValidationException($form);
        }

        // 2. save user in db

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // 3. create JWT-Token
        $token = $this->getUserToken($user);

        return new JsonResponse(['token' => $token], 200);
    }

    /**
     * @ApiDoc(
     *     section="Security",
     *     description="test",
     *     resource=true
     * )
     *
     * @Route("/api/token/refresh", name="api_refresh_token")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshAction(Request $request)
    {
        die($this->get('gesdinet.jwtrefreshtoken.refresh_token_manager')->getLastFromUsername('thomas'));
    }

    /**
     * Create a JSON-Web-Token with the username.
     *
     * @param User $user
     * @return string
     */
    private function getUserToken(User $user) {
        return $this->get('lexik_jwt_authentication.encoder.default')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 30 // 1 hour expiration
            ]);
    }
}