<?php

namespace CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return object
     *
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();

            $email = $em->getRepository('CmsBundle:Settings')->findOneByName('general_email');

            $message = \Swift_Message::newInstance()
                ->setSubject('Formularz kontaktowy - ' . $request->get('name'))
                ->setFrom($request->get('mail'))
                ->setTo($email->getValue())
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        array('comment' => $request->get('comment'))
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);
        }

        return $this->render('templates/contact.html.twig', array(
            'metaTitle' => 'Strona kontaktowa',
            'metaDescription' => 'Podstrona z formularzem kontaktowym',
            'metaKeywords' => 'kontakt, formularz, serwis'
        ));
    }

    /**
     * @param Request $request
     *
     * @return object
     *
     * @Route("/news", name="news")
     */
    public function newsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('templates/news.html.twig', array(
            'news' => $em->getRepository('CmsBundle:News')->findByIsPublished(1, array('dateAdded' => 'DESC')),
            'metaTitle' => 'Strona z aktualnościami',
            'metaDescription' => 'Podstrona z listą aktualności',
            'metaKeywords' => 'aktualności, formularz, serwis'
        ));
    }

    /**
     * @param Request $request
     *
     * @return object
     *
     * @Route("/oauth2callback", name="oauth")
     */
    public function authAction(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();

            $email = $em->getRepository('CmsBundle:Settings')->findOneByName('general_email');

            $message = \Swift_Message::newInstance()
                ->setSubject('Formularz kontaktowy - ' . $request->get('name'))
                ->setFrom($request->get('mail'))
                ->setTo($email->getValue())
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        array('comment' => $request->get('comment'))
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);
        }

        return $this->render('templates/contact.html.twig', array(
            'metaTitle' => 'Strona kontaktowa',
            'metaDescription' => 'Podstrona z formularzem kontaktowym',
            'metaKeywords' => 'kontakt, formularz, serwis'
        ));
    }

    /**
     * @param Request $request
     *
     * @return object
     *
     * @Route("/{slug}", name="page")
     */
    public function pageAction(Request $request)
    {
        $slug = $request->get('slug');
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CmsBundle:Page')->findOneBySlug($slug);

        if($page) {
            return $this->render('templates/' . $page->getPageTemplate()->getName() . '.html.twig', array(
                'metaTitle' => $page->getMetaTitle(),
                'metaDescription' => $page->getMetaDescription(),
                'metaKeywords' => $page->getMetaKeywords(),
                'page' => $page
            ));
        } else {
            return $this->createNotFoundException();
        }
    }

    /**
     * Overwrite standard render function.
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $em = $this->getDoctrine()->getManager();

        $configuration = $em->getRepository('CmsBundle:Settings')->fetchSettings();

        $parameters = array_merge(
            array('configuration' => $configuration),
            $parameters
        );

        return parent::render($view, $parameters, $response);
    }

}
