<?php

namespace CmsBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use CmsBundle\Entity\MenuItem;
use CmsBundle\Entity\Menu;

/**
 * Menu controller.
 *
 * @Route("/admin/cms")
 */
class MenuController extends Controller
{
    /**
     * Lists all Menus entities.
     *
     * @param Request $request
     *
     * @return object
     *
     * @Route("/menus", name="menu_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();

        return $this->render('admin/menu/index.html.twig', array(
            'menus' => $em->getRepository('CmsBundle:Menu')->search($params)
        ));
    }

    /**
     * Menu tree.
     *
     * @param Request $request
     * @param Menu $menu
     *
     * @return object
     *
     * @Route("/menu/{id}", name="menu_tree")
     * @Method({"GET", "POST"})
     *
     */
    public function treeAction(Request $request, Menu $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $node = $this->getNodeFromRequest($request, $menu->getId());

        if($node instanceof RedirectResponse) {
            return $node;
        }

        $arrayTree = $em->getRepository('CmsBundle:MenuItem')->fetchTreeByMenuId($menu->getId(), $node);

        $newForm = $this->prepareNewForm($request, $menu);
        if($newForm instanceof RedirectResponse) {
            return $newForm;
        }

        $editForm = $this->prepareEditForm($request, $node);
        if($editForm instanceof RedirectResponse) {
            return $editForm;
        }

        return $this->render('admin/menu/tree.html.twig', array(
            'newForm' => $newForm,
            'editForm' => $editForm,
            'tree' => $this->json($arrayTree)->getContent(),
            'menu' => $menu,
            'node' => $node
        ));
    }

    /**
     * @param MenuItem $menuItem
     *
     * @return RedirectResponse
     *
     * @Route("/menu/delete/{id}", name="menu_delete_item")
     * @Method("GET")
     */
    public function deleteAction(MenuItem $menuItem) {
        $menuId = $menuItem->getMenu()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('CmsBundle:MenuItem')->deleteBranch($menuItem);

        $this->addFlash('success', 'Pozycja w menu została poprawnie usunięta.');
        return $this->redirectToRoute('menu_tree', array('id' => $menuId));
    }

    /**
     * @param MenuItem $menuItem
     * @param string $direction
     *
     * @return RedirectResponse
     *
     * @Route("/menu/move/{id}/{direction}", name="menu_move")
     * @Method("GET")
     */
    public function moveAction(MenuItem $menuItem, $direction) {
        $em = $this->getDoctrine()->getManager();

        $position = $menuItem->getPosition();

        if($direction == 'up') {
            if($menuItem->getPosition() == 1) {
                return $this->redirectToRoute('menu_tree', array(
                    'node' => $menuItem->getId(),
                    'id' => $menuItem->getMenu()->getId()
                ));
            }
            $mod = -1;
        } else {
            $mod = 1;
        }

        $menuItem->setPosition($position + $mod);

        $em->persist($menuItem);

        if($menuItem->getParent()) {
            $query = $em->getRepository('CmsBundle:MenuItem')->createQueryBuilder('m')
                ->where('m.position = :position AND m.parent = :parent')
                ->setParameter('position', $position + $mod)
                ->setParameter('parent', $menuItem->getParent()->getId())
                ->orderBy('m.position', 'ASC')
                ->getQuery();
        } else {
            $query = $em->getRepository('CmsBundle:MenuItem')->createQueryBuilder('m')
                ->where('m.position = :position AND m.parent IS NULL')
                ->setParameter('position', $position + $mod)
                ->orderBy('m.position', 'ASC')
                ->getQuery();
        }


        foreach($query->getResult() as $result) {
            $result->setPosition($position);
            $em->persist($result);
        }

        $em->flush();

        $this->addFlash('success', 'Pozycja w menu została zmieniona.');
        return $this->redirectToRoute('menu_tree', array(
            'node' => $menuItem->getId(),
            'id' => $menuItem->getMenu()->getId()
        ));
    }

    /**
     * @param Request $request
     * @param MenuItem $menuItem
     *
     * @return \Symfony\Component\Form\Form
     */
    private function prepareEditForm(Request $request, MenuItem $menuItem) {
        $em = $this->getDoctrine()->getManager();
        $type = ucfirst($menuItem->getType());

        $editForm = $this->createForm('CmsBundle\Form\MenuItem' . $type . 'Type', $menuItem);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($menuItem);
            $em->flush();

            $this->addFlash('success', 'Pozycja w menu została zaktualizowana.');

            return $this->redirectToRoute('menu_tree', array(
                'id' => $menuItem->getMenu()->getId(),
                'node' => $menuItem->getId()
            ));
        }

        return $editForm->createView();
    }

    /**
     * @param Request $request
     * @param Menu $menu
     *
     * @return Form|boolean
     */
    private function prepareNewForm(Request $request, Menu $menu) {
        $em = $this->getDoctrine()->getManager();

        $menuItem = new MenuItem();
        $newForm = $this->createForm('CmsBundle\Form\MenuItemType', $menuItem);
        $newForm->handleRequest($request);

        if($newForm->isSubmitted() && $newForm->isValid()) {
            $lastPosition = $em->getRepository('CmsBundle:MenuItem')->getLastPosition();

            $menuItem->setPosition(100);
            $menuItem->setMenu($menu);
            $menuItem->setPosition($lastPosition + 1);

            if($menuItem->getType() == 'contact') {
                $menuItem->setUrl('contact');
            } elseif($menuItem->getType() == 'news') {
                $menuItem->setUrl('news');
            }

            $em->persist($menuItem);
            $em->flush();

            $this->addFlash('success', 'Pozycja w menu została dodana.');

            return $this->redirectToRoute('menu_tree', array(
                'id' => $menu->getId(),
                'node' => $menuItem->getId()
            ));
        }

        return $newForm->createView();
    }

    /**
     * @param $request
     * @param $menuId
     *
     * @return MenuItem|RedirectResponse
     */
    private function getNodeFromRequest($request, $menuId) {
        $em = $this->getDoctrine()->getManager();
        $nodeId = $request->query->get('node');

        if(!$nodeId) {
            $mainPage = $em->getRepository('CmsBundle:MenuItem')->findOneByMenu($menuId, array('parent' => 'ASC', 'id' => 'ASC'));

            if($mainPage) {
                return $this->redirectToRoute('menu_tree', array('id' => $menuId, 'node' => $mainPage->getId()));
            } else {
                $node = new MenuItem();
            }
        } else {
            $node = $em->getRepository('CmsBundle:MenuItem')->findOneById($nodeId);
        }

        return $node;
    }
}
