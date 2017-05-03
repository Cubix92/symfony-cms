<?php

namespace CmsBundle\Repository;

use \CmsBundle\Entity\MenuItem;

/**
 * MenuItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuItemRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Recursive tree generator
     *
     * @param int $nodeId
     * @param array $menuItems
     * @param int $treeDepth
     *
     * @return array
     */
    private function generateTree($nodeId, $menuItems, $treeDepth = 0) {
        $tree = array();

        if ( !empty ( $menuItems[ $nodeId ] ) )
        {
            foreach ( $menuItems[ $nodeId ] as $child )
            {
                $tmp = $child;
                $currentRow = $this->generateTree( $child[ 'key' ], $menuItems, $treeDepth + 1 );

                if ( !empty( $currentRow ) )
                {
                    $tmp[ 'uiIcon' ] = 'fa fa-folder';
                    $tmp[ 'children' ] = $currentRow;
                }
                $tree[] = $tmp;
            }
        }

        return $tree;
    }

    /**
     * Fetch tree for tree.js plugin
     *
     * @param int $menuId
     * @param MenuItem $node
     *
     * @return array
     */
    public function fetchTreeByMenuId($menuId, MenuItem $node = null) {
        $rows = $this->findByMenu($menuId, array('position' => 'ASC'));
        $menuItems = null;

        foreach ( $rows as $row )
        {
            $parent = $row->getParent() ? $row->getParent()->getId() : 0;
            $menuItems[ $parent ][] = array(
                'key' => $row->getId(),
                'text' => $row->getName(),
                'href' => '?node=' . $row->getId(),
                'isExpanded' => true,
                'uiIcon' => 'fa fa-file-text',
                'id' => $node->getId() == $row->getId() ? 'active' : ''
            );
        }

        return $this->generateTree(0, $menuItems);
    }

    /**
     * Fetch menu items for menu generator
     *
     * @param string $slug
     *
     * @return array
     */
    public function fetchMenuBySlug($slug) {
        $query = $this->createQueryBuilder('mi')
            ->join('mi.menu', 'm')
            ->where('m.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('mi.position', 'ASC')
            ->getQuery();

        $rows = $query->getResult();

        $menuItems = null;

        foreach ( $rows as $row )
        {
            if($row->getIsVisible()) {
                $parent = $row->getParent() ? $row->getParent()->getId() : 0;
                $menuItems[ $parent ][] = array(
                    'key' => $row->getId(),
                    'name' => $row->getName(),
                    'in_blank' => $row->getInBlank(),
                    'url' => $row->getUrl()
                );
            }
        }

        return $this->generateTree(0, $menuItems);
    }

    /**
     * Delete all branch recursively
     *
     * @param MenuItem $menuItem
     */
    public function deleteBranch(MenuItem $menuItem) {
        if($menuItem->getChildren()) {
            foreach($menuItem->getChildren() as $children) {
                $this->deleteBranch($children);
            }
        }
        $this->getEntityManager()->remove($menuItem);
        $this->getEntityManager()->flush();
    }

    public function getLastPosition($parentId = null) {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('count(m.id)');

        if($parentId) {
            $query = $queryBuilder->where('m.parent = :parent')
                ->setParameter('parent', $parentId)
                ->getQuery();
        } else {
            $query = $queryBuilder->where('m.parent IS NULL')
                ->getQuery();
        }

        return $query->getSingleScalarResult();
    }

}

