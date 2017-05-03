<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\GalleryPhoto;

/**
 * GalleryPhoto controller.
 *
 * @Route("/admin/cms/gallery/{gallery_id}")
 */
class GalleryPhotoController extends Controller
{
    /**
     * Lists all GalleryPhoto entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/photos", name="photo_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository('CmsBundle:Gallery')->findOneById($request->get('gallery_id'));

        $params = array_merge(
            array('galleryId' => $gallery->getId()),
            $request->query->all()
        );

        return $this->render('admin/gallery_photo/index.html.twig', array(
            'photos' => $em->getRepository('CmsBundle:GalleryPhoto')->search($params),
            'gallery' => $gallery
        ));
    }

    /**
     * Creates a new GalleryPhoto entity.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/photo", name="photo_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository('CmsBundle:Gallery')->findOneById($request->get('gallery_id'));

        $photo = new GalleryPhoto();
        $form = $this->createForm('CmsBundle\Form\GalleryPhotoType', $photo, array(
            'validation_groups' => array('new_file'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $photo->getFilename();
            $uploadPath = '/upload/gallery/' . $gallery->getId();
            $filename = $this->get('app.file_uploader')->upload($file, $uploadPath);

            if($filename) {
                $this->createNotFoundException("Nie udało się wgrać pliku.");
            }

            $photo->setGallery($gallery)
                ->setFilename($filename)
                ->setPath($uploadPath)
                ->setOriginalName($file->getClientOriginalName())
                ->setSize($file->getClientSize());

            $em->persist($photo);
            $em->flush();

            $this->addFlash('success', 'Zdjęcie zostało dodane.');
            return $this->redirectToRoute('photo_index', array('gallery_id' => $gallery->getId()));
        }

        return $this->render('admin/gallery_photo/new.html.twig', array(
            'gallery' => $gallery,
            'photo' => $photo,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing GalleryPhoto entity.
     *
     * @param Request $request
     * @param GalleryPhoto $photo
     *
     * @return object
     *
     * @Route("/photo/{id}", name="photo_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, GalleryPhoto $photo)
    {
        $em = $this->getDoctrine()->getManager();
        $filename = $photo->getFilename();

        $editForm = $this->createForm('CmsBundle\Form\GalleryPhotoType', $photo);
        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid()) {

            if($photo->getFilename()) {
                $filename = $this->get('app.file_uploader')->upload(
                    $photo->getFilename(),
                    '/upload/gallery/' . $photo->getGallery()->getId(),
                    array(),
                    $filename
                );
            }

            $photo->setFilename($filename);
            $em->persist($photo);
            $em->flush();

            $this->addFlash('success', 'Zdjęcie zostało zaktualizowane.');
            return $this->redirectToRoute('photo_index', array('gallery_id' => $photo->getGallery()->getId()));
        }

        return $this->render('admin/gallery_photo/edit.html.twig', array(
            'gallery' => $photo->getGallery(),
            'photo' => $photo,
            'form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a GalleryPhoto entity.
     *
     * @param GalleryPhoto $photo
     *
     * @return object
     *
     * @Route("/photo/delete/{id}", name="photo_delete")
     * @Method("GET")
     */
    public function deleteAction(GalleryPhoto $photo)
    {
        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository('CmsBundle:Gallery')->findOneById($photo->getGallery()->getId());
        $uploadPath = '/upload/gallery/' . $gallery->getId() . '/';
        $removed = $this->get('app.file_uploader')->remove($photo->getFilename(), $uploadPath);

        if($removed) {
            $em->remove($photo);
            $em->flush();

            $this->addFlash('success', 'Zdjęcie zostało usunięte.');
        } else {
            $this->addFlash('error', 'Wystąpił błąd i nie udało się usunąć pliku.');
        }

        return $this->redirectToRoute('photo_index', array('gallery_id' => $gallery->getId()));
    }
}
