<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 05.04.2017
 * Time: 16:39
 */

namespace AppBundle\Security;


use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManager $em
     * @param RouterInterface $router
     * @param UserPasswordEncoder $passwordEncoder
     */
    function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory     = $formFactory;
        $this->em              = $em;
        $this->router          = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * 1. step: get data
     * @param Request $request
     * @return mixed|void
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if(!$isLoginSubmit){
            // skip authentication
            return;
        }

        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);

        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data["_username"]
        );

        return $data;
    }

    /**
     * 2. step: find user
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return \AppBundle\Entity\User|null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials["_username"];

        return $this->em->getRepository("AppBundle:User")
            ->findOneBy(["username" => $username]);
    }

    /**
     * 3. step: check password
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials["_password"];

        if($this->passwordEncoder->isPasswordValid($user, $password)){
            return true;
        }

        return false;
    }

    /**
     * if authentication fails go to /login
     */
    protected function getLoginUrl()
    {
        return $this->router->generate("login");
    }

    use TargetPathTrait;

    /**
     * on authentication-success
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if(!$targetPath){
            $targetPath = $this->router->generate('homepage');
        }
        return new RedirectResponse($targetPath);
    }
}