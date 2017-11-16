<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 13/11/2017
 * Time: 14:58
 */

namespace AppBundle\Service;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Info;
use AppBundle\Form\Type\CommandeType;
use AppBundle\Form\Type\InfoType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManager;

class Achat
{
    protected $session;

    protected $doctrine;

    protected $form;

    protected $stripe;

    protected $mail;

    public function __construct(
        EntityManager $doctrine,
        Session $session,
        FormFactory $form,
        Stripe $stripe,
        EnvoieMail $mail
    )
    {
        $this->session = $session;
        $this->form = $form;
        $this->doctrine = $doctrine;
        $this->stripe = $stripe;
        $this->mail = $mail;
    }

    public function step1(Request $request) {

        $commande = new Commande();

        $commandeForm = $form = $this->form->create(CommandeType::class,$commande);
        $commandeForm->handleRequest($request);

        if ($commandeForm->isSubmitted() && $commandeForm->isValid()) {
            $data = $commandeForm->getData();
            $request->getSession()->set('command', $data);
            $dateEntree = ($request->getSession()->get('command')->getDateEntree());
            dump(($this->getNbrBilletJour($dateEntree)));

            if ($this->getNbrBilletJour($dateEntree) + count($commande->getQuantite()) > 1000) {

                $response = new RedirectResponse('/');
                $response->send();

            }
            $response = new RedirectResponse('step-2');
            $response->send();
        }
        return $form->createView();
    }

    public function step2(Request $request){

        $commande = $request->getSession()->get('command');
        dump($commande);
        if ($commande !== null) {
            $quantite = $request->getSession()->get('command')->getQuantite();

            for ($i = 1 ; $i <= $quantite ; $i++) {
                $infos[] = new info();
            }

            $infoForm = $form = $this->form->create(CollectionType::class, $infos, ['entry_type'=>InfoType::class]);
            $infoForm->handleRequest($request);

            if ($infoForm->isSubmitted() && $infoForm->isValid()) {

                for ($i = 0 ; $i < $quantite ; $i ++) {

                    $infos[$i];
                }

                $request->getSession()->set('info', $infos);

                $response = new RedirectResponse('step-3');
                $response->send();
            }
            return $form->createView();
        }
        $response = new RedirectResponse('/');
        $response->send();
    }

    public function step3(Request $request)
    {

        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');

        if ($commande !== null && $info !== null) {

            $prixCommande = $commande->getPrixCommande();

            if ($commande->getPrixCommande() !== null && $info[0]->getTarif() !== null)  {

                if ($request->isMethod('POST')) {
                    $token = $request->get('stripeToken');
                    try {
                        $this->stripe->setCard(
                            $this->stripe->getApiKey(),
                            $token,
                            $prixCommande
                        );

                        $quantite = $commande->getQuantite();
                        $this->doctrine->persist($commande);

                        for ($i = 0; $i < $quantite; $i++) {

                            $this->doctrine->persist($info[$i]);
                        }
                        $this->doctrine->flush();
                        $this->mail->sendMail($request);
                        $response = new RedirectResponse('step-4');
                        $response->send();

                    } catch(\Stripe\Error\Card $e) {

                        $response = new RedirectResponse('step-3');
                        $response->send();
                    }
                }
                return array ($commande, $info);
            }
            $this->session->getFlashBag()->add("problem-age", "Attention il faut au moins une personne majeur");
            $response = new RedirectResponse('step-2');
            $response->send();
        }
        $response = new RedirectResponse('/');
        $response->send();
    }

    public function getNbrBilletJour($dateEntree)
    {
        return count(
            $this->doctrine->getRepository(Info::class)->getBilletJour($dateEntree)
        );
    }
}