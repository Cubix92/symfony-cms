<?php

namespace CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;
/**
 * GalleryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GalleryRepository extends EntityRepository
{
    /**
     * @param $data
     *
     * @return array
     */
    public function search($data) {

        // INICJALIZACJA
        $query = $this->createQueryBuilder('g');

        // WYSZUKIWANIE
        if(isset($data['key'])) {
            $query->where('g.name LIKE :key OR g.description LIKE :key')
                ->setParameter('key', '%'.$data['key'].'%');
        }

        // SORTOWANIE
        if(isset($data['sort'])) {
            $params = explode('.', $data['sort']);

            if (isset($params[1])) {
                $query->orderBy('g.'.$params[0], $params[1]);
            } else {
                $query->orderBy('g.'.$params[0]);
            }

        } else {
            $query->orderBy('g.id', 'ASC');
        }

        return $query->getQuery()->getResult();
    }
}
