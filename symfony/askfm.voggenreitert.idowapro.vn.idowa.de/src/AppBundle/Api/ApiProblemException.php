<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 24.04.2017
 * Time: 13:17
 */

namespace AppBundle\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    /**
     * @var ApiProblem
     */
    private $apiProblem;

    /**
     * ApiProblemException constructor.
     * @param ApiProblem $apiProblem
     * @param \Exception|null $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem = $apiProblem;
        $statusCode       = $apiProblem->getStatusCode();
        $message          = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem() {
        return $this->apiProblem;
    }
}