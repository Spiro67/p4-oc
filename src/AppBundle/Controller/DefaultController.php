<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Info;
use AppBundle\Service\CalculPrix;
use AppBundle\Service\Stripe;
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

            if ($this->getNbrBilletJour($dateEntree) + $commande->getQuantite() > 1000) {

                $this->addFlash("nbr-billet", "Déjà plus de 1000 billets vendu pour cette date ! Merci de selectionner une autre date d'entrée");
                return $this->redirectToRoute('homepage');
            }
            return $this->redirectToRoute('step2');
        }
        return $this->render('main/index.html.twig', array (
            'commandeForm' => $commandeForm->createView(),
        ));
    }

    /**
     * @Route("/step-2", name="step2")
     */
    public function step2Action(Request $request)
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
            return $this->render('main/step2.html.twig', array (
                'infoForm' => $infoForm->createView(),
                ));
        }
        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/step-3", name="step3")
     *
     */

    public function step3Action(Request $request)
    {
        $this->get('app.prix')->setTarifBillet($request);
        $requete = $this->get('app.achat')->step3($request);
        $commande = $requete[0];
        $info =  $requete[1];
        $token = $this->get('app.stripe')->getApiToken();

                    return $this->render('main/step3.html.twig', array(
                        'commande' => $commande,
                        'info' => $info,
                        'token' => $token,
                    ));
    }

    /**
     * @Route("/step-4", name="step4")
     */

    public function step4Action(request $request)
    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');

        if ($commande !== null && $info !== null) {

            //$request->getSession()->clear();
            return $this->render('main/step4.html.twig');
        }

        return $this->redirectToRoute("homepage");
    }

    public function getNbrBilletJour($dateEntree)
    {
        return count(
            $this->getdoctrine()->getRepository(Info::class)->getBilletJour($dateEntree)
        );
    }

}