<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserCreateType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

private $encoder;

public function __construct(UserPasswordEncoderInterface $encoder)
{
    $this->encoder = $encoder;
}

    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository('App:User')->findAll()]);
    }


    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $username = $user->getUsername();
          $repository = $this->getDoctrine()->getRepository(User::class);
          $usernameUsed = $repository->findOneBy(['username' => $username]);

          if (empty($usernameUsed)) {
            $em = $this->getDoctrine()->getManager();

            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setRoles(["ROLE_USER"]);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été ajouté.');

            return $this->redirectToRoute('homepage');
          }

          else {
            $this->addFlash('error', 'Ce nom d\'utilisateur est déjà utiliser');
            return $this->render('user/create.html.twig', ['form' => $form->createView()]);
          }

        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }


    public function editAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $id]);

        if (!empty($user)) {
          $form = $this->createForm(UserEditType::class, $user);
          $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $this->encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', "L'utilisateur a bien été modifié");

                return $this->redirectToRoute('user_list');
            }
          }
          else {
            $this->addFlash('error', 'Cet utilisateur n\'existe pas');
            return $this->redirectToRoute('user_list');
          }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    public function deleteAction($id)
    {
      $repository = $this->getDoctrine()->getRepository(User::class);
      $user = $repository->findOneBy(['id' => $id]);
      if (!empty($user)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été supprimée.');

            return $this->redirectToRoute('user_list');
        }
      else {
        $this->addFlash('error', 'Cet utilisateur n\'existe pas');
        return $this->redirectToRoute('user_list');
      }
    }
}
