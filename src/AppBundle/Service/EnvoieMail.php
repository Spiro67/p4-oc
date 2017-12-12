<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 09/11/2017
 * Time: 15:41
 */


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class EnvoieMail
{

    private $mailer;
    private $templating;

    public function __construct (\Swift_Mailer $mailer, EngineInterface $templating) {

        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendMail (Request $request)

    {
        $commande = ($request->getSession()->get('command'));
        $info = $request->getSession()->get('info');

        $mail = (new \Swift_Message())
            ->setSubject('Musée du Louvre - Billeterie')
            ->setFrom('david@zielinger.fr')
            ->setTo($commande->getEmail())
            ->setBody($this->templating->render(
                'main/email.html.twig', [
                    'commande' => $commande,
                    'info' => $info,
                ]
            ), 'text/html');

        $this->mailer->send($mail);
    }

}