<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{

    public function listAction()
    {
        $user = $this->getUser();

        return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findAll(), 'user' => $user]);
    }


    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $userid = $user->getId();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(['id' => $userid]);
            $task->setUserCreate($user);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    public function editAction(Task $task, Request $request)
    {
        $taskid = $task->getId();
        $user = $this->getUser();
        $userid = $user->getId();
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->findOneBy(['id' => $taskid, 'userCreate' => $userid]);

        if (!empty($task)) {
          $form = $this->createForm(TaskType::class, $task);

          $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'La tâche a bien été modifiée.');

                return $this->redirectToRoute('task_list');
            }
        }
        else {
          $this->addFlash("error", "Cette tâche ne vous appartient pas");
          return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }


    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }


    public function deleteTaskAction(Task $task)
    {
      $taskid = $task->getId();
      $user = $this->getUser();
      $userid = $user->getId();
      $repository = $this->getDoctrine()->getRepository(Task::class);
      $task = $repository->findOneBy(['id' => $taskid, 'userCreate' => $userid]);

      if (!empty($task)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list');
        }
      else {
        $this->addFlash("error", "Cette tâche ne vous appartient pas");
        return $this->redirectToRoute('task_list');
      }
    }
}
