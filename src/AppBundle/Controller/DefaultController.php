<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */

    public function indexAction(Request $request)
    {
        $commandeForm = $this->get('app.achat')->step1($request);

        return $this->render('main/index.html.twig', array (
            'commandeForm' => $commandeForm,
    ));
    }

    /**
     * @Route("/step-2", name="step2")
     */
    public function step2Action(Request $request)
    {
        $infoForm = $this->get('app.achat')->step2($request);

        return $this->render('main/step2.html.twig', array (
            'infoForm' => $infoForm,
        ));
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

            $request->getSession()->clear();
            return $this->render('main/step4.html.twig');
        }

        return $this->redirectToRoute("homepage");
    }
}