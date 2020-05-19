<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{

    public function listAction()
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $role = $user->getRoles();
        $titre = 'Tâches à faire';
        $variables['url'] = $_SERVER['REQUEST_URI'];
        if ($role = 'ROLE_ADMIN') {
          return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findNotDoneAdmin($userId), 'user' => $user, 'titre' => $titre, 'url' => $variables['url']]);
        }
        else {
          return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findNotDone($userId), 'user' => $user, 'titre' => $titre, 'url' => $variables['url']]);
        }
    }

    public function listIsDoneAction()
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $role = $user->getRoles();
        $titre = 'Tâches terminer';
        $variables['url'] = $_SERVER['REQUEST_URI'];
        if ($role = 'ROLE_ADMIN') {
          return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findIsDoneAdmin($userId), 'user' => $user, 'titre' => $titre, 'url' => $variables['url']]);
        }
        else {
          return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findIsDone($userId), 'user' => $user, 'titre' => $titre, 'url' => $variables['url']]);
        }
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


    public function editAction($id, Request $request)
    {
      $repository = $this->getDoctrine()->getRepository(Task::class);
      $task = $repository->findOneBy(['id' => $id]);

        if (!empty($task)) {
          $taskid = $task->getId();
          $user = $this->getUser();
          $userid = $user->getId();
          $roles = $user->getRoles();
          $repository = $this->getDoctrine()->getRepository(Task::class);
          $taskvalid = $repository->findOneBy(['id' => $taskid, 'userCreate' => $userid]);

          if (!empty($taskvalid) or $roles[0] == 'ROLE_ADMIN') {
            $form = $this->createForm(TaskType::class, $task);

            $form->handleRequest($request);

              if ($form->isSubmitted() && $form->isValid()) {
                  $this->getDoctrine()->getManager()->flush();

                  $this->addFlash('success', 'La tâche a bien été modifiée.');

                  return $this->redirectToRoute('task_list');
              }
          }
          else {
            $this->addFlash('error', 'Cette tâche ne vous appartient pas');
            return $this->redirectToRoute('task_list');
          }
        }

        else {
          $this->addFlash('error', 'Cette tâche n\'éxiste pas');
          return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }


    public function toggleTaskAction($id)
    {
      $repository = $this->getDoctrine()->getRepository(Task::class);
      $task = $repository->findOneBy(['id' => $id]);

      if (!empty($task)) {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $value = $task->getIsDone();

        if ($value == '0') {
          $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));

          return $this->redirectToRoute('task_list_done');
        }
        elseif ($value == '1') {
          $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

          return $this->redirectToRoute('task_list');
        }
      }
      else {
        $this->addFlash('error', 'Cette tâche n\'éxiste pas');
        return $this->redirectToRoute('task_list');
      }
    }


    public function deleteTaskAction($id)
    {
      $repository = $this->getDoctrine()->getRepository(Task::class);
      $task = $repository->findOneBy(['id' => $id]);

      if (!empty($task)) {
        $taskid = $task->getId();
        $user = $this->getUser();
        $userid = $user->getId();
        $roles = $user->getRoles();
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $taskvalid = $repository->findOneBy(['id' => $taskid, 'userCreate' => $userid]);

        if (!empty($taskvalid) or $roles[0] == 'ROLE_ADMIN') {
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
      else {
        $this->addFlash('error', 'Cette tâche n\'éxiste pas');
        return $this->redirectToRoute('task_list');
      }
    }
}
