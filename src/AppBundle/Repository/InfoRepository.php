<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/10/2017
 * Time: 14:54
 */

namespace AppBundle\Repository;


class InfoRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBilletJour($dateEntree)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.commande', 'commande')
            ->addSelect('commande')
            ->where('commande.dateEntree = :dateTime')
            ->setParameter('dateTime', $dateEntree)
            ->getQuery()
            ->getResult();
    }
}