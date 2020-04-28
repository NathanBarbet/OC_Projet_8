<?php

namespace tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase

  {
      public function testTaskForm()
      {
          $formData = array(
              'title' => 'titre',
              'content' => 'contenu',
          );

          $form = $this->factory->create(TaskType::class);

          $object = new Task();
          $object->setTitle($formData["title"]);
          $object->setContent($formData["content"]);

          $form->submit($formData);

          $this->assertTrue($form->isSynchronized());
          $this->assertEquals($object->getTitle(), $form->getData()['title']);
          $this->assertEquals($object->getContent(), $form->getData()['content']);

          $view = $form->createView();

      }
  }
