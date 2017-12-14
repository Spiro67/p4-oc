<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 12/12/2017
 * Time: 15:34
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\info;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures implements FixtureInterface

{
    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 20; $i++) {
            $info = new info();
            $info->setDateNaissance(new \DateTime("01/01/1950"));
            $info->setNom("Test");
            $info->setPrenom("1000Jours");
            $info->setPays("France");
            $info->setTarif(16);
            $manager->persist($info);
        }

        $manager->flush();
    }
}