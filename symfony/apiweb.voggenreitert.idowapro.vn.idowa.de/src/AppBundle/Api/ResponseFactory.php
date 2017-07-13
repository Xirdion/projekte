<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 10:29
 */

namespace AppBundle\Api;


use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    /**
     * @param ApiProblem $apiProblem
     * @return JsonResponse
     */
    public function createResponse(ApiProblem $apiProblem)
    {
        $data = $apiProblem->toArray();

        // making type a URL, to a temporarily fake page
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://apiweb.voggenreitert.idowapro.vn.idowa.de/docs/errors#'.$data['type'];
        }

        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }
}