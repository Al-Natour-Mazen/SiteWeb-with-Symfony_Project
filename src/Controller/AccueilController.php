<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app')]
class AccueilController extends AbstractController
{
    /***************************************************/
    /*                Page D'accueil
    /***************************************************/
    #[Route('', name: '_accueil')]
    public function indexAction(): Response
    {
        return $this->render('Vue/Accueil/accueil.html.twig');
    }

    /***************************************************/
    /*                Le Menu
    /***************************************************/
    public function menuAction(): Response
    {
        $args = array(
            'isAuth'=> false,
            'isAdmin' => false,
            'isSuperAdmin' => true,
            'isClient' => false,
        );
        return $this->render('Layouts/menu.html.twig', $args);
    }

}
