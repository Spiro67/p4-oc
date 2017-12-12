<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains("Billeterie", $client->getResponse()->getContent());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Billeterie")')->count()
        );
    }

    public function testSubmitFormAction () {

        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Suivant')->form();
        $form['commande[dateEntree]'] = '18-12-2017';
        $form['commande[typeBillet]'] = 'Journée complète';
        $form['commande[quantite]'] = 2;
        $form['commande[email]'] = 'test@test.fr';

        $client->submit($form);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/step-2'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Informations")')->count());
    }
    /**
     * @dataProvider urlProvider
     */
    public function testRedirectHomepageAction ($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertTrue(
            $client->getResponse()->isRedirect('/'));
    }

    public function urlProvider()
    {
        return array(
            array('/step-2'),
            array('/step-3'),
            array('/step-4'),
        );
    }

    public function testRescueEmailAction () {

        $client = static::createClient();

        $crawler = $client->request('GET', '/renvoie');

        $form = $crawler->selectButton('Rechercher')->form();

        $form['renvoie_commande[dateEntree]'] = '14-12-2017';
        $form['renvoie_commande[email]'] = 'spiro67290@gmail.com';

        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Musée du Louvre - Billeterie', $message->getSubject());
        $this->assertEquals('david@zielinger.fr', key($message->getFrom()));

        $this->assertEquals(1, $mailCollector->getMessageCount());

        $this->assertTrue(
            $client->getResponse()->isRedirect('/'));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

    }
}
