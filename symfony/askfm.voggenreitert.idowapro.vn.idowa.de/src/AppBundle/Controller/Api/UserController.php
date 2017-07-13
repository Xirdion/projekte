<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 10:29
 */

namespace AppBundle\Controller\Api;


use AppBundle\Controller\BaseController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Question\QuestionStatus;
use Doctrine\ORM\EntityManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 *
 * @Security("is_granted('ROLE_USER')")
 */
class UserController extends BaseController
{
    /**
     * Show the user's profile.
     *
     * @ApiDoc(
     *     section="User",
     *     description="Show an users's profile.",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirements"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users/{username}", name="api_user_show")
     * @Method("GET")
     *
     * @param string $username
     * @return Response
     */
    public function showAction(string $username)
    {
        /** @var User $user */
        $user = $this->getUserByUsername($username);

        return $this->createApiResponse($user, 200);
    }

    /**
     * Create a new User.
     *
     * @ApiDoc(
     *     section="User",
     *     description="Create a new user.",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="true" },
     *          { "name"="email",    "dataType"="string", "description"="email",    "requirement"="true" },
     *          { "name"="password", "dataType"="string", "description"="password", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users", name="api_user_new")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->createApiResponse($user, 201);
    }

    /**
     * List all available users.
     *
     * @ApiDoc(
     *     section="User",
     *     description="List all available users.",
     *     parameters={
     *          { "name"="filter", "dataType"="string", "description"="filter username", "required"=false },
     *          { "name"="sort",   "dataType"="string", "description"="sort-direction",  "required"=false }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users", name="api_users_list")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter', '');
        $sort   = $request->query->get('sort', '');

        $qb = $this->getUserRepository()
            ->createUserQueryBuilder($filter, $sort);

        $paginationCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_users_list');

        return $this->createApiResponse($paginationCollection, 200);
    }

    /**
     * Delete one user identified by its Id.
     *
     * @ApiDoc(
     *     section="User",
     *     description="Delete one user identified by its Id.",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users/{username}", name="api_user_delete")
     * @Method("DELETE")
     *
     * @param string $username
     * @return JsonResponse
     */
    public function deleteAction(string $username)
    {
        /** @var User $user */
        $user = $this->getUserByUsername($username, false);

        if ($user) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return new JsonResponse(null, 204);
    }

    /**
     * Get all answered questions this user has received.
     *
     * @ApiDoc(
     *     section="User",
     *     description="Get all answered questions this user has received",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users/{username}/questions", name="api_user_questions")
     * @Method("GET")
     *
     * @param string $username
     * @param Request $request
     * @return Response
     */
    public function questionAction(string $username, Request $request)
    {
        /** @var User $user */
        $user = $this->getUserByUsername($username);

        $qb = $this->getQuestionRepository()
            ->createQuestionQueryBuilder($user, QuestionStatus::Answered);

        $paginationCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_user_questions', ['username' => $username]);

        return $this->createApiResponse($paginationCollection, 200);
    }

    /**
     * Update the user.
     *
     * @ApiDoc(
     *     section="User",
     *     description="Update the user.",
     *     requirements={
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="true" }
     *     },
     *     parameters={
     *          { "name"="username", "dataType"="string", "description"="username", "required"=false },
     *          { "name"="email",    "dataType"="string", "description"="email",    "required"=false },
     *          { "name"="password", "dataType"="string", "description"="password", "required"=false }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/users({username}", name="api_user_edit")
     * @Method("PATCH")
     *
     * @param string $username
     * @param Request $request
     * @return Response
     */
    public function editAction(string $username, Request $request)
    {
        /** @var User $user */
        $user = $this->getUserByUsername($username);

        $form = $this->createForm(UserType::class, $user);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->createApiResponse($user, 200);
    }

    /**
     * @param string $username
     * @param bool $check
     * @return User
     */
    private function getUserByUsername(string $username, bool $check = true)
    {
        /** @var User $user */
        $user = $this->getUserRepository()
            ->findUserByUsername($username);

        if ($check && !$user) {
            throw $this->createNotFoundException(sprintf('No user found with username "%s".', $username));
        }

        return $user;
    }
}