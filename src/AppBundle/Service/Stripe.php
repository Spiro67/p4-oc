<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 30/10/2017
 * Time: 15:53
 */

namespace AppBundle\Service;

use Stripe\Charge;
use Stripe\Error\Card;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManager;

class Stripe
{

    protected $session;

    protected $doctrine;

    protected $form;

    protected $mail;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiToken;

    /**
     * Stripe constructor.
     *
     * @param string $apiKey
     * @param string $apiToken
     */
    public function __construct(
        $apiKey,
        $apiToken,
        EntityManager $doctrine,
        Session $session,
        FormFactory $form,
        EnvoieMail $mail
    ) {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
        $this->session = $session;
        $this->form = $form;
        $this->doctrine = $doctrine;
        $this->mail = $mail;
    }

    public function paiement(Request $request)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');
        $prixCommande = $commande->getPrixCommande();

        if ($request->isMethod('POST')) {
            $token = $request->get('stripeToken');
            try {
                $this->setCard(
                    $this->getApiKey(),
                    $token,
                    $prixCommande
                );

                $quantite = $commande->getQuantite();
                $commande->setNumeroCommande(uniqid('LV',false ));
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

    public function setCard($api, $token, $prixCommande)
    {
        \Stripe\Stripe::setApiKey($api);

        try {
            Charge::create(array(
                'amount' => $prixCommande * 100,
                'currency' => 'EUR',
                'source' => $token,
                'description' => 'Musée du Louvre - Billeterie',
            ));
        } catch (Card $e) {
            $e->getMessage();
        }
    }

    /** @return string */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /** @return string */
    public function getApiToken()
    {
        return $this->apiToken;
    }
}