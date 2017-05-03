<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\Gallery;

/**
 * Gallery controller.
 *
 * @Route("/admin/cms")
 */
class GalleryController extends Controller
{
    /**
     * Lists all Gallery entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/galleries", name="gallery_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();

        return $this->render('admin/gallery/index.html.twig', array(
            'galleries' => $em->getRepository('CmsBundle:Gallery')->search($params)
        ));
    }

    /**
     * Creates a new Gallery entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/gallery", name="gallery_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $gallery = new Gallery();
        $form = $this->createForm('CmsBundle\Form\GalleryType', $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($gallery);
            $em->flush();

            $this->addFlash('success', 'Galeria została dodana.');
            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('admin/gallery/new.html.twig', array(
            'gallery' => $gallery,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Gallery entity.
     *
     * @param Request $request
     * @param Gallery $gallery
     *
     * @return object
     *
     * @Route("/gallery/{id}", name="gallery_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Gallery $gallery)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('CmsBundle\Form\GalleryType', $gallery);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($gallery);
            $em->flush();

            $this->addFlash('success', 'Galeria została zaktualizowana.');
            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('admin/gallery/edit.html.twig', array(
            'gallery' => $gallery,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Gallery entity.
     *
     * @param Gallery $gallery
     *
     * @return object
     *
     * @Route("/gallery/delete/{id}", name="gallery_delete")
     * @Method("GET")
     */
    public function deleteAction(Gallery $gallery)
    {
        $em = $this->getDoctrine()->getManager();

        if (count($gallery->getPhotos())) {
            $this->addFlash('warning', 'Nie możesz usunąć galerii, która nie jest pusta.');
            return $this->redirectToRoute('gallery_index');
        } elseif($em->getRepository('CmsBundle:Page')->findOneByGallery($gallery)) {
            $this->addFlash('warning', 'Nie możesz usunąć galerii, która została przypisana do strony.');
            return $this->redirectToRoute('gallery_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($gallery);
        $em->flush();

        $this->addFlash('success', 'Galeria została usunięta.');
        return $this->redirectToRoute('gallery_index');
    }
}
