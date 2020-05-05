<?php

namespace tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManagerInterface;

class UserControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp()
    {
        $this->client = self::createClient();
    }

    public function logIn($username, $password)
    {
      $crawler = $this->client->request('GET', '/login');

      $this->client->submitForm('login', [
          '_username' => $username,
          '_password' => $password,
      ]);

      $crawler = $this->client->followRedirect();
    }

    public function testAdminDisplayListUser()
    {
        $username = 'username';
        $password = 'password';
        $this->logIn($username, $password);
        $crawler = $this->client->request('GET', '/users');

        $nBUser = count($this->getContainer()->get('doctrine')->getRepository(User::class)->findAll());

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
        self::assertContains('/users/create', $crawler->filter('a')->extract(['href']));
        self::assertContains('/logout', $crawler->filter('a')->extract(['href']));

        self::assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur")')->count());
        self::assertSame(1, $crawler->filter('html:contains("Adresse")')->count());
        self::assertSame(1, $crawler->filter('html:contains("Actions")')->count());

        self::assertSame($nBUser + 1, $crawler->filter('tr')->count());
    }

    public function testAccessDeniedForUserPageListUser()
    {
        $username = 'test';
        $password = 'password';
        $this->logIn($username, $password);
        $this->client->request('GET', '/users');

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithoutAuth()
    {
        $user = $this->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('username');
        $this->client->request('GET', '/users/edit/'.$user->getId().'');
        self::assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

        $username = 'test';
        $password = 'password';
        $this->logIn($username, $password);
        $this->client->request('GET', '/users/edit/'.$user->getId().'');
        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        $username = 'username';
        $password = 'password';
        $this->logIn($username, $password);
        $this->client->request('GET', '/users/edit/'.$user->getId().'');
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserOk()
    {
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user_create[username]'] = 'userTestCreate';
        $form['user_create[password][first]'] = 'password';
        $form['user_create[password][second]'] = 'password';
        $form['user_create[email]'] = 'userTestCreate@gmail.com';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("L\'utilisateur a bien été ajouté")')->count());
    }

    public function testErrorCreateUser()
    {
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user_create[username]'] = 'username';
        $form['user_create[password][first]'] = 'password';
        $form['user_create[password][second]'] = 'badPassword';
        $form['user_create[email]'] = 'test@test.fr';
        $crawler = $this->client->submit($form);

        self::assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre !")')->count());
        self::assertSame(1, $crawler->filter('html:contains("Cette valeur est déjà utilisée.")')->count());
    }

    public function testDeleteUser()
    {
        $this->logIn('username', 'password');

        $user = $this->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('userTestCreate');

        $crawler = $this->client->request('GET', '/users');

        $this->client->request('GET', '/users/delete/'.$user->getId().'');
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(0, $crawler->filter('html:contains("userTestCreate")')->count());
        self::assertSame(1, $crawler->filter('html:contains("L\'utilisateur a bien été supprimée.")')->count());

    }

    private function getContainer()
    {
        self::bootKernel();
        return self::$container;
    }
}
