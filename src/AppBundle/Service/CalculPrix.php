<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/09/2017
 * Time: 14:22
 */

namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


// Entity
use AppBundle\Entity\Ticket;

class CalculPrix
{

    private $prixBillet;

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

    public function setTarifBillet(Request $request)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');
        if ($commande !== null && $info !== null) {
            $quantite = $commande->getQuantite();
            $prixCommande = 0;
            $isMajeur = false;

            for ($i = 0; $i < $quantite; $i++) {

                $dateNaissance = $info[$i]->getDateNaissance();
                $dateEntree = $commande->getDateEntree();
                $diff = date_diff($dateNaissance, $dateEntree);

                if ($diff->format('%y years') >= 18) {

                    $isMajeur = true;

                }
            }

            if ($isMajeur === true) {

                for ($i = 0; $i < $quantite; $i++) {

                    $dateNaissance = $info[$i]->getDateNaissance();
                    $dateEntree = $commande->getDateEntree();
                    $diff = date_diff($dateNaissance, $dateEntree);
                    $tarifReduit = $info[$i]->getTarifReduit();

                    $this->prixBillet = $this->setAge($diff->format('%y years'), "");

                    if ($tarifReduit === true && $this->prixBillet > 10) {

                        $this->prixBillet = "10";
                    }

                    $prixCommande = $prixCommande + $this->prixBillet;
                    $commande->setPrixCommande($prixCommande);
                    $info[$i]->setTarif($this->prixBillet);
                    $info[$i]->setCommande($commande);
                }
                return $commande;
            }
        }
    }
    public function setAge($age)
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
            default:
                $this->prixBillet = 16;
                break;
        }
        return $this->prixBillet;
    }

}