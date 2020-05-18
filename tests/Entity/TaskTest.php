<?php

namespace tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

    public function testSetCreatedAt()
    {
        $task = new Task();

        $task->setCreatedAt(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
    }

    public function testTitle()
    {
        $task = new Task();

        $task->setTitle('title');
        $this->assertSame('title', $task->getTitle());
    }

    public function testContent()
    {
        $task = new Task();

        $task->setContent('content');
        $this->assertSame('content', $task->getContent());
    }

    public function testToggle()
    {
        $task = new Task();

        $task->toggle(true);
        $this->assertEquals(true, $task->isDone());
    }

    public function testisDone()
    {
        $task = new Task();

        $task->setIsDone(true);
        $this->assertEquals(true, $task->IsDone());
    }

    public function testUser()
    {
        $task = new Task();
        $user = new User();

        $task->setUserCreate($user);
        $this->assertEquals($user, $task->getUserCreate());
    }
}
