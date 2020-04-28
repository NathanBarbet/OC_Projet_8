<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    public function indexAction()
    {
        $user = $this->getUser();
        return $this->render('default/index.html.twig');
    }
}
