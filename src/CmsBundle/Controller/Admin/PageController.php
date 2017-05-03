<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\Page;

/**
 * Page controller.
 *
 * @Route("/admin/cms")
 */
class PageController extends Controller
{
    /**
     * Lists all Page entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/pages", name="page_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();

        return $this->render('admin/page/index.html.twig', array(
            'pages' => $em->getRepository('CmsBundle:Page')->search($params)
        ));
    }

    /**
     * Creates a new Page entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/page", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $page = new Page();
        $form = $this->createForm('CmsBundle\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'Strona została dodana.');
            return $this->redirectToRoute('page_index');
        }

        return $this->render('admin/page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @param Request $request
     * @param Page $page
     *
     * @return object
     *
     * @Route("/page/{id}", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('CmsBundle\Form\PageType', $page)->remove('slug');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'Strona została zaktualizowana.');
            return $this->redirectToRoute('page_index');
        }

        return $this->render('admin/page/edit.html.twig', array(
            'page' => $page,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Page entity.
     *
     * @param Page $page
     *
     * @return object
     *
     * @Route("/page/delete/{id}", name="page_delete")
     * @Method("GET")
     */
    public function deleteAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository('CmsBundle:MenuItem')->findOneByUrl($page->getSlug());
        if($item) {
            $this->addFlash('warning', 'Nie można usunąć strony, która jest dodana do menu. Usuń najpierw stronę z menu.');
            return $this->redirectToRoute('page_index');
        }

        $em->remove($page);
        $em->flush();

        $this->addFlash('success', 'Strona została usunięta.');
        return $this->redirectToRoute('page_index');
    }
}
