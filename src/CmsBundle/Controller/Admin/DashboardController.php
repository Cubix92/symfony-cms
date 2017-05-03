<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed;
     *
     * @Route("/admin/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        $clientSecrets = $this->generateClientSecretsPath();
        $results = array();

        if (file_exists($clientSecrets)) {
            $client = $this->get('ga.analytics_service')->prepareClient($clientSecrets);

            if($client) {
                $results = $this->get('ga.analytics_service')->fetchData($client);

                if(!$results) {
                    $this->addFlash('warning', 'Niepoprawny plik dostępowy.');
                }
            } else {
                $this->addFlash('warning', 'Żeby skorzystać z modułu do statystyk proszę wgrać w ustawieniach odpowiedni plik dostępowy.');
            }
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'results' => $results
        ]);
    }

    /**
     * @Route("/admin/help", name="help")
     */
    public function helpAction()
    {
        return $this->render('admin/dashboard/help.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     *
     * @Route("/admin/bar", name="bar")
     */
    public function barAction(Request $request) {

        $client = $this->get('ga.analytics_service')->prepareClient($this->generateClientSecretsPath());

        if($client) {
            return $this->json($this->get('ga.analytics_service')->fetchData($client));
        } else {
            return false;
        }

    }

    /**
     * @return string
     */
    private function generateClientSecretsPath() {
        $em = $this->getDoctrine()->getManager();
        $clientSecrets = $em->getRepository('CmsBundle:Settings')->findOneByName('client_secrets');

        if($clientSecrets) {
            return $this->get('kernel')->getRootDir() . '/../web/upload/settings/client_secrets/' . $clientSecrets->getValue();

        }
    }

}
