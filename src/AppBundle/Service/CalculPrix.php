<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/09/2017
 * Time: 14:22
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Workflow\Exception\LogicException;

// Entity
use AppBundle\Entity\Commande;
use AppBundle\Entity\Ticket;

class CalculPrix
{
    /**
     * @var int
     */
    private $prixBillet;

    /** @var Session */
    protected $session;



    public function __construct(Session $session) {

        $this->session = $session;
    }

    /**
     *
     * @param int  $age
     * @param bool $reduction
     *
     * @return int
     */

    public function setTarifCommande(Request $request)
    {

    }

    public function setTarifBillet(Request $request)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');
        $quantite = $commande->getQuantite();
        $prixCommande = 0;

        for ($i = 0; $i < $quantite; $i++) {

            $test = $info[$i]->getDateNaissance();
            $date2=$commande->getDateEntree();
            $diff=date_diff($test,$date2);
            $tarifReduit = $info[$i]->getTarifReduit();

            if ($tarifReduit !== true) {

                $this->prixBillet = $this->setAge($diff->format('%y years'), "");

            } else {

                $this->prixBillet = "10";
            }

            $prixCommande = $prixCommande + $this->prixBillet;
            $commande->setPrixCommande($prixCommande);
            $info[$i]->setTarif($this->prixBillet);
            $info[$i]->setCommande($commande);
        }

        return $commande;
    }
    public function setAge($age, $tarifReduit)
    {
        switch ($age) {
            case $age <= 4:
                $this->prixBillet = 0;
                break;
            case $age > 4 && $age <= 12:
                $this->prixBillet = 8;
                break;
            case $age >= 60:
                $this->prixBillet = 12;
                break;
            case $tarifReduit :
                $this->prixBillet = 10;
                break;
            default:
                $this->prixBillet = 16;
                break;
        }
        return $this->prixBillet;
    }

}