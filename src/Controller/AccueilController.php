<?php

namespace App\Controller;

use App\Entity\User;
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

        // $client = $this->getUser();

        //on le fait en dure pour le moment
        $userRepository = $em->getRepository(User::class);
        $client = $userRepository->findOneBy(['login' => 'simon']);

        $nbproduits = 0;
        // On compte tous les produits dans son panier
        if($client)
            $nbproduits = count($client->getOrders());

        $args = array(
            'isAuth'=> true,
            'isAdmin' => false,
            'isSuperAdmin' => true,
            'isClient' => false,
            'nbproduit' => $nbproduits,
        );
        return $this->render('Layouts/menu.html.twig', $args);
    }

}
