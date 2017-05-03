<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\Category;

/**
 * Category controller.
 *
 * @Route("/admin/cms")
 */
class CategoryController extends Controller
{
    /**
     * Lists all Categories entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/categories", name="category_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();

        return $this->render('admin/category/index.html.twig', array(
            'categories' => $em->getRepository('CmsBundle:Category')->search($params)
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/category", name="category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category();
        $form = $this->createForm('CmsBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Kategoria została dodana.');
            return $this->redirectToRoute('category_index');
        }

        return $this->render('admin/category/new.html.twig', array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @param Request $request
     * @param Category $category
     *
     * @return object
     *
     * @Route("/category/{id}", name="category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('CmsBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Kategoria została zaktualizowana.');
            return $this->redirectToRoute('category_index');
        }

        return $this->render('admin/category/edit.html.twig', array(
            'category' => $category,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Category entity.
     *
     * @param Category $category
     *
     * @return object
     *
     * @Route("/category/delete/{id}", name="category_delete")
     * @Method("GET")
     */
    public function deleteAction(Category $category)
    {
        if (count($category->getNews())) {
            $this->addFlash('warning', 'Nie możesz usunąć kategorii, która ma dodane wpisy.');
            return $this->redirectToRoute('category_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Kategoria została usunięta.');
        return $this->redirectToRoute('category_index');
    }
}
