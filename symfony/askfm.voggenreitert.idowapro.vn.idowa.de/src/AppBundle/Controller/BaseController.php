<?php

namespace AppBundle\Controller;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\QuestionRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

abstract class BaseController extends Controller
{
    /**
     * Is the current user logged in?
     *
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        return $this->container->get('security.authorization_checker')
            ->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Logs this user into the system
     *
     * @param User $user
     */
    public function loginUser(User $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
    }

    /**
     * Used to find the fixtures user - I use it to cheat in the beginning
     *
     * @param $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getDoctrine()
            ->getRepository('AppBundle:User');
    }

    /**
     * @return QuestionRepository
     */
    protected function getQuestionRepository()
    {
        return $this->getDoctrine()
            ->getRepository('AppBundle:Question');
    }

    /**
     * @param $data
     * @param string $format
     * @return mixed
     */
    protected function serialize($data, $format = 'json') {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $request = $this->get('request_stack')->getCurrentRequest();
        $groups  = array('Default');
        if ($request->query->get('deep')) {
            $groups[] = 'deep';
        }
        $context->setGroups($groups);

        return $this->container->get('jms_serializer')
            ->serialize($data, 'json', $context);
    }

    /**
     * Create a JSON-Response with Content-Type = application/hal+json
     *
     * @param $data
     * @param int $statusCode
     * @return Response
     */
    protected function createApiResponse($data, int $statusCode = 200) {
        $json = $this->serialize($data);
        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/hal+json'
        ));
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     */
    protected function processForm(Request $request, FormInterface $form) {
        /** @var array */
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            $apiProblem = new ApiProblem(400, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);

            throw new ApiProblemException($apiProblem);
            //throw new HttpException(400, 'Invalid JSON body!');
        }

        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    /**
     * @param FormInterface $form
     */
    protected function throwApiProblemValidationException(FormInterface $form) {
        $errors = $this->getErrorsFromForm($form);

        $apiProblem = new ApiProblem(400, ApiProblem::TYPE_VALIDATION_ERROR);
        $apiProblem->set('errors', $errors);

        throw new ApiProblemException($apiProblem);

        /*$response = new JsonResponse($apiProblem->toArray(), $apiProblem->getStatusCode());
        $response->headers->set('Content-Type', 'application/problem+json');
        return $response;*/
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form) {
        $errors = array();
        // get all error messages for this form
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        // look if there are sub-forms (fields) and collect their errors
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
