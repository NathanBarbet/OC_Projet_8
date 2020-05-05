<?php

namespace tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp()
    {
        $this->client = self::createClient();
    }

    public function testLoginFunctionWithCorrectLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->client->submitForm('login', [
            '_username' => 'username',
            '_password' => 'password',
        ]);

        $crawler = $this->client->followRedirect();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !")')->count());
    }

    public function testDisplayMessageErrorLoginIncorrectUsername()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->client->submitForm('login', [
            '_username' => 'badUser',
            '_password' => 'password',
        ]);

        $crawler = $this->client->followRedirect();

        self::assertSame(1, $crawler->filter('html:contains("Identifiants invalides.")')->count());
    }

    public function testDisplayMessageErrorLoginIncorrectPassword()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->client->submitForm('login', [
            '_username' => 'username',
            '_password' => 'badPassword',
        ]);

        $crawler = $this->client->followRedirect();

        self::assertSame(1, $crawler->filter('html:contains("Identifiants invalides.")')->count());
    }

    public function testDisplayMessageErrorLoginIncorrectUsernameAndPassword()
    {
        $crawler = $this->client->request('GET', '/login');

        $this->client->submitForm('login', [
            '_username' => 'badUsername',
            '_password' => 'badPassword',
        ]);

        $crawler = $this->client->followRedirect();

        self::assertSame(1, $crawler->filter('html:contains("Identifiants invalides.")')->count());
    }
}
