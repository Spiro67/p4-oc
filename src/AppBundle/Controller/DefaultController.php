<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Info;
use AppBundle\Service\CalculPrix;
use AppBundle\Form\Type\CommandeType;
use AppBundle\Form\Type\InfoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Workflow\Exception\LogicException;

class DefaultController extends Controller
{

    /** @var $PrixBillet */

    protected $prixBillet;

    /** @var $infos */

    protected $infos;

    /**
     * @Route("/", name="homepage")
     */

    public function indexAction(Request $request)
    {
        $commande = new Commande();


        $commandeForm = $this->createForm(CommandeType::class,$commande);
        $commandeForm->handleRequest($request);

        if ($commandeForm->isSubmitted() && $commandeForm->isValid()) {
            $data = $commandeForm->getData();
            $request->getSession()->set('command', $data);
            $dateEntree = ($request->getSession()->get('command')->getDateEntree());
            dump(($this->getNbrBilletJour($dateEntree)));

                if ($this->getNbrBilletJour($dateEntree) + count($commande->getQuantite()) > 1000) {
                    return $this->redirectToRoute("homepage");
                }

            return $this->redirectToRoute("step2");
        }

        return $this->render('main/index.html.twig', array (
            'commandeForm' => $commandeForm->createView(),
    ));
    }

    /**
     * @Route("/step-2", name="step2")
     */
    public function step2(Request $request)
    {
        $commande = $request->getSession()->get('command');
        if ($commande !== null) {
            $quantite = $request->getSession()->get('command')->getQuantite();

            for ($i = 1 ; $i <= $quantite ; $i++) {
                $infos[] = new info();
            }

            $infoForm = $this->createForm(CollectionType::class, $infos, ['entry_type'=>InfoType::class]);
            $infoForm->handleRequest($request);

            if ($infoForm->isSubmitted() && $infoForm->isValid()) {

                for ($i = 0 ; $i < $quantite ; $i ++) {

                    $infos[$i];
                }

                $request->getSession()->set('info', $infos);

                return $this->redirectToRoute("step3");
            }

            return $this->render('main/step2.html.twig', array(
                'infoForm' => $infoForm->createView(),
            ));
        }

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/step-3", name="step3")
     */

    public function step3(Request $request)
    {

        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');

        if ($commande->getPrixCommande() > 0) {

            if ($commande !== null && $info !== null) {


                $this->get('app.prix')->setTarifBillet($request);

                $token = $this->get('app.stripe')->getApiToken();

                dump($commande);
                dump($info);
                dump($token);

                return $this->render('main/step3.html.twig', array(
                    'commande' => $commande,
                    'info' => $info,
                    'token' => $token,
                ));

            }
            return $this->redirectToRoute("homepage");
        }
        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/step-4", name="step4")
     */

    public function step4(request $request, \Swift_Mailer $mailer)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');

        if ($commande !== null && $info !== null) {

            $mail = (new \Swift_Message())
                ->setSubject('Musée du Louvre - Billeterie')
                ->setFrom('david@zielinger.fr')
                ->setTo($commande->getEmail())
                ->setBody($this->renderView(
                    'main/email.html.twig', [
                        'commande' => $commande,
                        'info' => $info,
                    ]
                ), 'text/html');

            $mailer->send($mail);
            $request->getSession()->clear();
            return $this->render('main/step4.html.twig');
        }

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route(
     *     "/checkout",
     *     name="order_checkout",
     *     methods="POST"
     * )
     */

    public function checkoutAction(Request $request)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');
        $prixCommande = $request->getSession()->get('command')->getPrixCommande();
        $apiKey= $this->get('app.stripe')->getApiKey();
        dump($apiKey);
        \Stripe\Stripe::setApiKey($apiKey);

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $prixCommande * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Billeterie louvre"
            ));

            $quantite = $commande->getQuantite();
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);

            for ($i = 0; $i < $quantite; $i++) {

                $em->persist($info[$i]);
            }
            $em->flush();

            return $this->redirectToRoute("step4");

        } catch(\Stripe\Error\Card $e) {

            $this->addFlash("error","Snif ça marche pas :(");
            return $this->redirectToRoute("step3");
            // The card has been declined
        }
    }

    public function getNbrBilletJour($dateEntree)
    {
        return count(
            $this->getDoctrine()->getRepository(Info::class)->getBilletJour($dateEntree)
        );
    }
}