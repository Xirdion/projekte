<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 10:29
 */

namespace AppBundle\Controller\Api;


use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Controller\BaseController;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use AppBundle\Form\AnswerType;
use AppBundle\Form\Model\QuestionModel;
use AppBundle\Form\QuestionType;
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
 * Class QuestionController
 * @package AppBundle\Controller\Api
 *
 * @Security("is_granted('ROLE_USER')")
 */
class QuestionController extends BaseController
{
    /**
     * Create a question for another user. The currently logged-in user gets the author.
     *
     * @ApiDoc(
     *     section="Question",
     *     description="Create a new question for another user.",
     *     requirements={
     *          { "name"="question", "dataType"="string", "description"="question", "requirement"="true" },
     *          { "name"="username", "dataType"="string", "description"="username", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/questions", name="api_question_create")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var QuestionModel $questionModel */
        $questionModel = new QuestionModel();
        $form = $this->createForm(QuestionType::class, $questionModel);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }

        $question = new Question();
        $question->setQuestion($questionModel->getQuestion());
        $question->setUser($questionModel->getUser());
        $question->setAuthor($this->getUser());

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();

        return $this->createApiResponse($question, 201);
    }

    /**
     * List all qustions the user has received.
     *
     * @ApiDoc(
     *     section="Question",
     *     description="List all Questions with a special status ordered by createdAt.",
     *     parameters={
     *          { "name"="status", "dataType"="string",  "description"="filter question-status", "required"="false"},
     *          { "name"="sort",   "dataType"="string" , "description"="sort direction",         "required"="false"}
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/questions", name="api_questions_list")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $status = $request->query->get('status');
        // check if it's a valid status
        $status = ($status != QuestionStatus::Answered && $status != QuestionStatus::Deleted && $status != QuestionStatus::All) ? QuestionStatus::Unanswered : $status;
        $sort   = $request->query->get('sort', '');

        $qb = $this->getQuestionRepository()
            ->createQuestionQueryBuilder($user, $status, $sort);

        $paginationCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_questions_list');

        return $this->createApiResponse($paginationCollection, 200);
    }

    /**
     * Get one question identified by its Id.
     *
     * @ApiDoc(
     *     section="Question",
     *     description="Show one single Question.",
     *     requirements={
     *          { "name"="id", "dataType"="string", "description"="questionId", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/questions/{id}", name="api_question_show")
     * @Method("GET")
     *
     * @param string $id
     * @return Response
     */
    public function showAction(string $id)
    {
        /** @var Question $question */
        $question = $this->getQuestionRepository()
            ->find($id);

        if (!$question) {
            throw $this->createNotFoundException('No question found!');
        }

        return $this->createApiResponse($question, 200);
    }

    /**
     * Delete one question identified by its Id.
     *
     * @ApiDoc(
     *     section="Question",
     *     description="Delete one single Question",
     *     requirements={
     *          { "name"="id", "dataType"="string", "description"="questionId", "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/questions/{id}", name="api_question_delete")
     * @Method("DELETE")
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteAction(string $id)
    {
        /** @var Question $question */
        $question = $this->getQuestionRepository()
            ->find($id);

        if ($question) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();
        }
        // even return a success-response when no question was found (no question = deleted)
        return new JsonResponse(null, 204);
    }

    /**
     * Create an answer for a question.
     *
     * @ApiDoc(
     *     section="Question",
     *     description="Create an answer for a question.",
     *     requirements={
     *          { "name"="id",     "dataType"="string", "description"="questionId", "requirement"="true" },
     *          { "name"="answer", "dataType"="string", "description"="answer",     "requirement"="true" }
     *     },
     *     resource=true
     * )
     *
     * @Route("/api/questions/{id}", name="api_question_answer")
     * @Method("PATCH")
     *
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function answerAction(string $id, Request $request)
    {
        /** @var Question $question */
        $question = $this->getQuestionRepository()
            ->find($id);

        if (!$question) {
            throw $this->createNotFoundException('No question found!');
        }

        // check if the question is already ansered
        if ($question->getStatus() === QuestionStatus::Answered) {
            $apiProblem = new ApiProblem(400, ApiProblem::TYPE_QUESTION_ALREADY_ANSWERED);
            throw new ApiProblemException($apiProblem);
        }

        $form = $this->createForm(AnswerType::class, $question);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form); // throw exception
        }

        $question->setStatus(QuestionStatus::Answered);

        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();

        return $this->createApiResponse($question, 200);
    }
}