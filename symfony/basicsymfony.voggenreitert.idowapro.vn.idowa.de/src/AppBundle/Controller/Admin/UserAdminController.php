<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 07.04.2017
 * Time: 16:23
 */

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\User;
use AppBundle\Form\UserDeleteForm;
use AppBundle\Form\UserForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserAdminController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserAdminController extends Controller
{
    /**
     * @var int
     */
    private $limit = 30;

    /**
     * @Route("/users", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userlistAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createForm(UserDeleteForm::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $data = $deleteForm->getData();
            $delUsers = explode(',', $data['selectedusers']);

            if (count($delUsers) > 0) {
                $repository = $em->getRepository('AppBundle:User');
                $admins     = $repository->getAllAdmins();

                // check if there will be at least one admin left
                $allowDelete = false;
                foreach ($admins as $admin) {
                    if (!in_array($admin['id'], $delUsers)) {
                        $allowDelete = true;
                        break;
                    }
                }
                if ($allowDelete) {
                    $repository->deleteUsersById($delUsers);
                    $this->addFlash('success', 'Benutzer wurden gelöscht.');
                } else {
                    $this->addFlash('danger', '<strong>Benutzer konnte nicht gelöscht werden</strong>: Es muss mindestens ein Administrator vorhanden sein.');
                }
            } else {
                $this->addFlash('warning', 'kein Benutzer ausgewählt.');
            }
            return $this->redirectToRoute('admin_users');
        }

        $session = $request->getSession();

        $sortCol = $request->get('sort');
        $sortDir = $request->get('sortdir');

        if ($sortCol && $sortDir) {
            $sort = ['col'=>$sortCol, 'dir'=>strtoupper($sortDir)];
            $session->set('usersort', $sort);
        } else {
            $sort = $session->get('usersort');

            $sortCol = $sort['col'];
            $sortDir = $sort['dir'];
        }


        // this get automatically updated when there are users deleted or a new user is added (pageload)
        $users = $em->getRepository('AppBundle:User')->loadUsers($this->limit, 0, '', $sort);

        //$users = $em->getRepository('AppBundle:User')->findAll();
        return $this->render('admin/users/users.html.twig', [
            'sortcol' => $sortCol,
            'sortdir' => $sortDir,
            'aUsers'  => $users,
            'deleteForm' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="admin_user_edit")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, User $user) {
        $pathToSecurity = $this->get('kernel')->getRootDir()."/config/security.yml";
        $aRoleList = $this->get('app.yaml_reader')->readArray($pathToSecurity, array("security", "role_hierarchy"));

        $form = $this->createForm(UserForm::class, $user, array(
            "roleList" => array_keys($aRoleList)
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Benutzer "'.$user->getUsername().'" aktualisiert.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/new", name="admin_user_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $pathToSecurity = $this->get('kernel')->getRootDir()."/config/security.yml";
        $aRoleList = $this->get('app.yaml_reader')->readArray($pathToSecurity, array("security", "role_hierarchy"));

        $form = $this->createForm(UserForm::class, null, array(
            "roleList" => array_keys($aRoleList)
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Benutzer "'.$user->getUsername().'" erstellt.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/new.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/search", name="admin_user_search")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function searchAction(Request $request) {
        if ($request->isXMLHttpRequest()) {
            $search  = $request->get('search');
            $session = $request->getSession();

            $oldSearch = $session->get('userserach');
            if ($oldSearch == $search) {
                // search has not changed
                return new JsonResponse([
                    'nochange' => true
                ]);
            } else {
                $session->set('usersearch', $search);

                $em         = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:User');

                $sort  = $session->get('usersort');
                $users = $repository->loadUsers($this->limit, 0, $search, $sort);

                return $this->render('admin/users/_usersList.html.twig', [
                    'aUsers' => $users
                ]);
            }
        }
        return new Response('This is not ajax!', 400);
    }

    /**
     * @Route("/users/loadmore", name="admin_user_load")
     * @param Request $request
     * @return Response
     */
    public function loadMoreAction(Request $request) {
        if ($request->isXMLHttpRequest()) {
            $session = $request->getSession();

            // TODO: maybe save current offset so that the same data is not loaded twice
            $em         = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('AppBundle:User');

            $offset = $request->get('offset');
            $search = $session->get('userserach');
            $sort   = $session->get('usersort');
            $users  = $repository->loadUsers($this->limit, $offset, $search, $sort);

            if (count($users) > 0) {
                return $this->render('admin/users/_usersList.html.twig', [
                    'aUsers' => $users
                ]);
            } else {
                // no records: end of table
                return new JsonResponse([
                    'end' => true
                ]);
            }
        }
        return new Response('This is not ajax!', 400);
    }
}