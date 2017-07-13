<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 08:35
 */

namespace AppBundle\Security;


use AppBundle\Api\ApiProblem;
use AppBundle\Api\ResponseFactory;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JWTEncoderInterface
     */
    private $JWTEncoder;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * JwtTokenAuthenticator constructor.
     * @param JWTEncoderInterface $JWTEncoder
     * @param EntityManager $entityManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(JWTEncoderInterface $JWTEncoder, EntityManager $entityManager, ResponseFactory $responseFactory)
    {
        $this->JWTEncoder      = $JWTEncoder;
        $this->entityManager   = $entityManager;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param Request $request
     * @return null|string
     */
    public function getCredentials(Request $request)
    {
        /** @var AuthorizationHeaderTokenExtractor $extractor */
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        /** @var string $token */
        $token = $extractor->extract($request);

        if (!$token) {
            return null; // stops the authentication (not fail just stop)
        }
        return $token;
    }


    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return \AppBundle\Entity\User|null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->JWTEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $exception) {
            // https://github.com/lexik/LexikJWTAuthenticationBundle/blob/05e15967f4dab94c8a75b275692d928a2fbf6d18/Exception/JWTDecodeFailureException.php
            throw new CustomUserMessageAuthenticationException($exception->getReason());
        }

        $username = $data['username'];

        return $this->entityManager
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // no user was found for the token

        $apiProblem = new ApiProblem(401);
        $apiProblem->set('detail', $exception->getMessageKey());

        return $this->responseFactory->createResponse($apiProblem);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // called when authentication info is missing from a request that requires it

        $apiProblem = new ApiProblem(401);
        // you could translate this
        $message = $authException ? $authException->getMessageKey() : 'Missing credentials';

        $apiProblem->set('detail', $message);

        return $this->responseFactory->createResponse($apiProblem);
    }
}