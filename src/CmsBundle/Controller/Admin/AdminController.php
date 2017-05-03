<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\Admin;

/**
 * Admin controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all Admin entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/admins", name="admin_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();

        return $this->render('admin/admin/index.html.twig', array(
            'admin' => $em->getRepository('CmsBundle:Admin')->findAll()
        ));
    }

    /**
     * Creates a new Admin entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/admin", name="admin_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $admin = new Admin();
        $form = $this->createForm('CmsBundle\Form\AdminType', $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($admin);
            $em->flush();

            $this->addFlash('success', 'Nowy użytkownik został dodany.');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/admin/new.html.twig', array(
            'admin' => $admin,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Admin entity.
     *
     * @param Request $request
     * @param Admin $admin
     *
     * @return object
     *
     * @Route("/admin/{id}", name="admin_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Admin $admin)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('CmsBundle\Form\AdminType', $admin);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($admin);
            $em->flush();

            $this->addFlash('success', 'Konto użytkownika zostało zmodyfikowane.');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/admin/edit.html.twig', array(
            'admin' => $admin,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Admin entity.
     *
     * @param Admin $admin
     *
     * @return object
     *
     * @Route("/admin/delete/{id}", name="admin_delete")
     * @Method("GET")
     */
    public function deleteAction(Admin $admin)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();

        $this->addFlash('success', 'Użytkownik został pomyślnie usunięty.');
        return $this->redirectToRoute('admin_index');
    }
}
