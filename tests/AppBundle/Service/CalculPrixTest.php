<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/11/2017
 * Time: 15:16
 */

namespace Tests\AppBundle\Service;

use AppBundle\Service\CalculPrix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class CalculPrixTest extends TestCase
{

    public function testPrice(){

        $dateEntree = new \DateTime("01/01/2018");
        $dateNaissanceSenior = new \DateTime("01/01/1950");
        $dateNaissanceAdulte = new \DateTime("01/01/1990");
        $dateNaissanceEnfant = new \DateTime("01/01/2008");
        $dateNaissanceGratuit = new \DateTime("01/01/2017");

        $ageSenior = date_diff($dateNaissanceSenior, $dateEntree);
        $ageAdulte  = date_diff($dateNaissanceAdulte, $dateEntree);
        $ageEnfant = date_diff($dateNaissanceEnfant, $dateEntree);
        $ageGratuit = date_diff($dateNaissanceGratuit, $dateEntree);

        $calculPrix = new CalculPrix($session = new Session(new MockArraySessionStorage()));

        $this->assertSame(12, $calculPrix->setAge($ageSenior->format('%y years'), ""));
        $this->assertSame(16, $calculPrix->setAge($ageAdulte->format('%y years'), ""));
        $this->assertSame(8, $calculPrix->setAge($ageEnfant->format('%y years'), ""));
        $this->assertSame(0, $calculPrix->setAge($ageGratuit->format('%y years'), ""));

    }

}