<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\News;

/**
 * News controller.
 *
 * @Route("/admin/cms/category/{category_id}")
 */
class NewsController extends Controller
{
    /**
     * Lists all News entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/list_news", name="news_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CmsBundle:Category')->findOneById($request->get('category_id'));

        $params = array_merge(
            array('categoryId' => $category->getId()),
            $request->query->all()
        );

        return $this->render('admin/news/index.html.twig', array(
            'news' => $em->getRepository('CmsBundle:News')->search($params),
            'category' => $category
        ));
    }

    /**
     * Creates a new News entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/news", name="news_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('CmsBundle:Category')->findOneById($request->get('category_id'));

        $news = new News();
        $form = $this->createForm('CmsBundle\Form\NewsType', $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setDateAdded(new \DateTime());
            $news->setCategory($category);

            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'Kategoria została dodana.');
            return $this->redirectToRoute('news_index', array('category_id' => $category->getId()));
        }

        return $this->render('admin/news/new.html.twig', array(
            'news' => $news,
            'category' => $category,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing News entity.
     *
     * @param Request $request
     * @param News $news
     *
     * @return object
     *
     * @Route("/news/{id}", name="news_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, News $news)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('CmsBundle\Form\NewsType', $news);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'Wpis został zaktualizowany.');
            return $this->redirectToRoute('news_index', array('category_id' => $news->getCategory()->getId()));
        }

        return $this->render('admin/news/edit.html.twig', array(
            'news' => $news,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a News entity.
     *
     * @param News $news
     *
     * @return object
     *
     * @Route("/news/delete/{id}", name="news_delete")
     * @Method("GET")
     */
    public function deleteAction(News $news)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($news);
        $em->flush();

        $this->addFlash('success', 'Aktualność została usunięta.');
        return $this->redirectToRoute('news_index', array('category_id' => $news->getCategoryId()));
    }
}
