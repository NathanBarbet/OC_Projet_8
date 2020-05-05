<?php

namespace tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp()
    {
        $this->client = self::createClient();
    }

    public function testClickButtonListTasksToDo()
    {
        $this->logIn('username', 'password');
        $crawler = $this->client->request('GET', '/');
        $_SERVER['REQUEST_URI'] = 'http://localhost/P8/public/tasks';

        $link = $crawler->selectLink('Consulter la liste des tâches à faire')->link();
        $crawler = $this->client->click($link);
  
        self::assertContains('/logout', $crawler->filter('a')->extract(['href']));
        self::assertContains('/tasks/create', $crawler->filter('a')->extract(['href']));
    }

    public function testClickButtonListTasksDone()
    {
        $this->logIn('username', 'password');
        $crawler = $this->client->request('GET', '/');
        $_SERVER['REQUEST_URI'] = 'http://localhost/P8/public/tasks/done';

        $link = $crawler->selectLink('Consulter la liste des tâches terminées')->link();
        $crawler = $this->client->click($link);

        self::assertContains('/logout', $crawler->filter('a')->extract(['href']));
        self::assertContains('/tasks/create', $crawler->filter('a')->extract(['href']));
    }

    public function testCreateTask()
    {
        $this->logIn('username', 'password');
        $crawler = $this->client->request('GET', '/');
        $_SERVER['REQUEST_URI'] = 'http://localhost/P8/public/tasks';

        $link = $crawler->selectLink('Créer une nouvelle tâche')->link();
        $crawler = $this->client->click($link);

        $this->assertSame(2, $crawler->filter('input')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'taskCreated';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("taskCreated")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée")')->count());


        $crawler = $this->client->click($link);

        $this->assertSame(2, $crawler->filter('input')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'taskEdit';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("taskEdit")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée")')->count());



        $crawler = $this->client->click($link);

        $this->assertSame(2, $crawler->filter('input')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'taskToggle';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("taskToggle")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée")')->count());
    }

    public function testEditTask()
    {
        $this->logIn('username', 'password');

        $task = $this->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('taskEdit');

        $crawler = $this->client->request('GET', '/tasks');

        $crawler = $this->client->request('GET', '/tasks/edit/'.$task->getId().'');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'editedTask';
        $form['task[content]'] = 'editedContent';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(1, $crawler->filter('html:contains("editedTask")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a bien été modifiée.")')->count());
    }

    public function testToggleTask()
    {
        $this->logIn('username', 'password');

        $task = $this->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('taskToggle');

        $crawler = $this->client->request('GET', '/tasks');

        $crawler = $this->client->request('GET', '/tasks/toggle/'.$task->getId().'');
        self::assertNotContains('/tasks/toggle/'.$task->getId().'', $crawler->filter('a')->extract(['href']));

        $crawler = $this->client->request('GET', '/tasks/1');
    }


    public function testDeleteTask()
    {
        $this->logIn('username', 'password');

        $task = $this->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('taskCreated');

        $crawler = $this->client->request('GET', '/tasks');

        $this->client->request('GET', '/tasks/delete/'.$task->getId().'');
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(0, $crawler->filter('html:contains("taskCreated")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());


        $task = $this->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('editedTask');

        $crawler = $this->client->request('GET', '/tasks');

        $this->client->request('GET', '/tasks/delete/'.$task->getId().'');
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(0, $crawler->filter('html:contains("editedTask")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());


        $task = $this->getContainer()->get('doctrine')->getRepository(Task::class)->findOneByTitle('taskToggle');

        $crawler = $this->client->request('GET', '/tasks');

        $this->client->request('GET', '/tasks/delete/'.$task->getId().'');
        $crawler = $this->client->followRedirect();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSame(0, $crawler->filter('html:contains("taskToggle")')->count());
        self::assertSame(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
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

    private function getContainer()
    {
        self::bootKernel();
        return self::$container;
    }
}
