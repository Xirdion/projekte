<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 24.04.2017
 * Time: 13:27
 */

namespace AppBundle\EventListener;


use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Api\ResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory, bool $debug = false)
    {
        $this->responseFactory = $responseFactory;
        $this->debug           = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) {
        // only reply to /api URLs
        if (strpos($event->getRequest()->getPathInfo(), '/api') !== 0) {
            return;
        }

        $e = $event->getException();
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

        if ($statusCode === 500 && $this->debug) {
            return;
        }

        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $apiProblem = new ApiProblem($statusCode);

            /*
             * If it's an HttpException message (e.g. for 404, 403), we'll say as a rule that the exception message is safe for the client.
             * Otherwise, it could be some sensitive low-level exception, which would *not* be exposed
             */
            if ($e instanceof HttpExceptionInterface) {
                $apiProblem->set('detail', $e->getMessage());
            }
        }

        $response = $this->responseFactory->createResponse($apiProblem);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}