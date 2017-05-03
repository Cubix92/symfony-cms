<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CmsBundle\Entity\Settings;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Settings controller.
 *
 * @Route("/admin/cms")
 */
class SettingsController extends Controller
{
    /**
     * Lists and modify all Settings.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/settings", name="settings")
     * @Method({"GET", "POST"})
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $oldSettings = $em->getRepository('CmsBundle:Settings')->fetchSettings();
        $form = $this->createForm('CmsBundle\Form\SettingsType', $oldSettings);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $newSettings = $form->getData();

            $updated = $this->updateSettings($newSettings, $oldSettings);

            if($updated) {
                $this->addFlash('success', 'Konfiguracja została zaktualizowana.');
            } else {
                $this->addFlash('error', 'Nie udało się zaktualizować konfiguracji.');
            }

            return $this->redirectToRoute('settings');
        }

        return $this->render('admin/settings/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Cleans choose setting.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/setting/clean", name="setting_clean", requirements={"_format": "json"})
     * @Method({"POST"})
     *
     */
    public function cleanAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');

        $setting = $em->getRepository('CmsBundle:Settings')->findOneByName($name);

        if($setting) {
            $uploadPath = '/upload/settings/' . $setting->getName() . '/';
            $removed = $this->get('app.file_uploader')->remove($setting->getValue(), $uploadPath);

            if(!$removed) {
                return $this->json(array('success' => 0, 'msg' => 'Nie udało się usunąć pliku.', 'name' => $name));
            }

            $setting->setValue(null);
            $em->persist($setting);
            $em->flush();

            return $this->json(array('success' => 1, 'name' => $name));
        }

        return $this->json(array('success' => 0, 'name' => $name));
    }

    /**
     * @param $newSettings
     * @param $oldSettings
     *
     * @return bool
     */
    private function updateSettings($newSettings, $oldSettings) {

        $em = $this->getDoctrine()->getManager();

        $settings = array_diff_assoc($newSettings, $oldSettings);

        foreach($settings as $settingName => $settingValue) {

            if(array_key_exists($settingName, $oldSettings)) {
                $setting = $em->getRepository('CmsBundle:Settings')->findOneByName($settingName);
            } else {
                $setting = new Settings();
            }

            if($settingValue instanceof UploadedFile) {
                if (!empty($oldSettings[$settingName])) {
                    $this->get('app.file_uploader')->remove($oldSettings[$settingName], '/upload/settings/' . $settingName);
                }
                $settingValue = $this->get('app.file_uploader')->upload($settingValue, '/upload/settings/' . $settingName);
            } else {
                if(preg_match('/^.*\.(png|jpg|jpeg|gif|pdf|bmp|json|txt)$/', $setting->getValue())) {
                    continue;
                }
            }

            $setting->setName($settingName);
            $setting->setValue($settingValue);

            $em->persist($setting);
        }

        $em->flush();

        return true;
    }
}
