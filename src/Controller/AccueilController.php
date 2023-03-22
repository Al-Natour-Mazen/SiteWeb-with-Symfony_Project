<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $nbproduits = 0;
        // On compte tous les produits dans son panier
        if($user !== null) {
            $nbproduits = count($user->getOrders());
            $isAuth = true;
            if ($this->isGranted('ROLE_SUPERADMIN'))
                $isSuperAdmin = true;
            if ($this->isGranted('ROLE_ADMIN'))
                $isAdmin = true;
            if ($this->isGranted('ROLE_CLIENT'))
                $isClient = true;
        }




        $args = array(
            'isAuth'=> $isAuth,
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'isClient' => $isClient,
            'nbproduit' => $nbproduits,
        );
        return $this->render('Layouts/menu.html.twig', $args);
    }

}
