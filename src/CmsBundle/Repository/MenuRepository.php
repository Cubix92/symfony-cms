<?php

namespace CmsBundle\Repository;

/**
 * MenuRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $data
     *
     * @return array
     */
    public function search($data) {

        // INICJALIZACJA
        $query = $this->createQueryBuilder('m');

        // WYSZUKIWANIE
        if(isset($data['key'])) {
            $query->where('m.name LIKE :key')->setParameter('key', '%'.$data['key'].'%');
        }

        // SORTOWANIE
        if(isset($data['sort'])) {
            $params = explode('.', $data['sort']);

            if (isset($params[1])) {
                $query->orderBy('m.'.$params[0], $params[1]);
            } else {
                $query->orderBy('m.'.$params[0]);
            }

        } else {
            $query->orderBy('m.id', 'ASC');
        }

        return $query->getQuery()->getResult();
    }
}
