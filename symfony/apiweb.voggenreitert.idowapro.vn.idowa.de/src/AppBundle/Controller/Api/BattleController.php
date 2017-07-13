<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 11:58
 */

namespace AppBundle\Controller\Api;


use AppBundle\Controller\BaseController;
use AppBundle\Form\BattleType;
use AppBundle\Form\Model\BattleModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class BattleController extends BaseController
{
    /**
     * @Route("/api/battles")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $battleModel = new BattleModel();

        $form = $this->createForm(BattleType::class, $battleModel, [
            'user' => $this->getUser()
        ]);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }
        $battle = $this->getBattleManager()->battle(
            $battleModel->getProgrammer(),
            $battleModel->getProject()
        );

        // TODO set Location-header
        return $this->createApiResponse($battle, 201);
    }
}