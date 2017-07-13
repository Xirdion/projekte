<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 19.04.2017
 * Time: 13:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use AppBundle\Form\ProgrammerType;
use AppBundle\Form\UpdateProgrammerType;
use AppBundle\Pagination\PaginationCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProgrammerController
 * @package AppBundle\Controller\Api
 *
 * @Security("is_granted('ROLE_USER')")
 */
class ProgrammerController extends BaseController
{
    /**
     * @Route("/api/programmers")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request) {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        $programmer = new Programmer();
        $form = $this->createForm(ProgrammerType::class, $programmer);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $programmer->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        $response = $this->createApiResponse($programmer, 201);
        $programmerUrl = $this->generateUrl('api_programmers_show', ['nickname' => $programmer->getNickname()]);
        $response->headers->set('Location', $programmerUrl);

        return $response;
    }

    /**
     * @Route("/api/programmers/{nickname}", name="api_programmers_show")
     * @Method("GET")
     * @param string $nickname
     * @return Response
     */
    public function showAction(string $nickname) {
        /**
         * @var Programmer
         */
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if (!$programmer) {
            throw $this->createNotFoundException(sprintf(
                'No programmer found with nickname "%s"', $nickname
            ));
        }
        return $this->createApiResponse($programmer, 200);
    }

    /**
     * @Route("/api/programmers", name="api_programmers_collection")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_programmers_collection');

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }

    /**
     * @Route("/api/programmers/{nickname}")
     * @Method({"PUT", "PATCH"})
     * @param string $nickname
     * @param Request $request
     * @return Response
     */
    public function updateAction(string $nickname, Request $request) {
        /**
         * @var Programmer
         */
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if (!$programmer) {
            throw $this->createNotFoundException(sprintf(
                'No programmer found with nickname "%s"', $nickname
            ));
        }

        $form = $this->createForm(UpdateProgrammerType::class, $programmer);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        return $this->createApiResponse($programmer, 200);
    }

    /**
     * @Route("/api/programmers/{nickname}")
     * @Method("DELETE")
     * @param string $nickname
     * @return JsonResponse
     */
    public function deleteAction(string $nickname) {
        /**
         * @var Programmer
         */
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);

        if ($programmer) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($programmer);
            $em->flush();
        }
        return new JsonResponse(null, 204);
    }

    /**
     * @Route("/api/programmers/{nickname}/battles", name="api_programmers_battles_list")
     * @Method("GET")
     *
     * @param Programmer $programmer
     * @param Request $request
     * @return Response
     */
    public function battlesListAction(Programmer $programmer, Request $request) {
        $battlesQb = $this->getDoctrine()
            ->getRepository('AppBundle:Battle')
            ->createQueryBuilderForProgrammer($programmer);

        $colltection = $this->get('pagination_factory')->createCollection(
            $battlesQb, $request, 'api_programmers_battles_list', ['nickname' => $programmer->getNickname()]
        );

        return $this->createApiResponse($colltection);
    }

    /**
     * @Route("/api/programmers/{nickname}/tagline")
     * @Method("PUT")
     *
     * @param Programmer $programmer
     * @param Request $request
     * @return Response
     */
    public function editTagLineAction(Programmer $programmer, Request $request)
    {
        $programmer->setTagLine($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        return $this->createApiResponse($programmer);
    }

    /**
     * @Route("/api/programmers/{nickname}/powerup")
     * @Method("POST")
     *
     * @param Programmer $programmer
     * @return Response
     */
    public function powerUpAction(Programmer $programmer)
    {
        $this->get('battle.power_manager')->powerUp($programmer);
        return $this->createApiResponse($programmer);
    }
}