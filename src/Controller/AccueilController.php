<?php

/********************************************/
/*          PROJET TECHNOLOGIE WEB 2        */
/*     AL NATOUR MAZEN && CAILLAUD TOM      */
/********************************************/

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
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
    public function menuAction(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $isAuth = false;
        $isAdmin = false;
        $isSuperAdmin = false;
        $isClient = false;
        $nameUser = "Anonyme";
        $nbproduits = 0;

        if($user !== null) {
            $nbproduits = count($user->getOrders()); // On compte tous les produits dans son panier
            $isAuth = true;
            $nameUser = $user->getNom() .' ' . $user->getPrenom();
            if ($this->isGranted('ROLE_SUPERADMIN'))
                $isSuperAdmin = true;
            if ($this->isGranted('ROLE_ADMIN'))
                $isAdmin = true;
            if ($this->isGranted('ROLE_CLIENT'))
                $isClient = true;
        }
        $args = array(
            'nameUser' =>$nameUser,
            'isAuth'=> $isAuth,
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'isClient' => $isClient,
            'nbproduit' => $nbproduits,
        );
        return $this->render('Layouts/menu.html.twig', $args);
    }

}
