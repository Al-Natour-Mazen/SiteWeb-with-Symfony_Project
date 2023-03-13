<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account', name: 'account_')]
class AccountController extends AbstractController
{
    /***************************************************/
    /*                Connexion d'un compte
    /***************************************************/
    #[Route('/connect', name: 'connect')]
    public function connectAction(Request $req): Response
    {
        if($req->request->count() > 0){
            // si on est là ça veut dire que l'utilisateur essaye de se co donc on va verifier s'il est dans la BD


            //si oui alors on le redirige vers l'accueil
            return $this->redirectToRoute('app_accueil');

        }

        return $this->render("Vue/Account/connect.html.twig");
    }

    /***************************************************/
    /*                Déconnexion d'un compte
    /***************************************************/
    #[Route('/disconnect', name: 'disconnect')]
    public function disconnectAction(): Response
    {
        $this->addFlash('info', 'Vous pourrez vous déconnecter ultérieurement');
        return $this->redirectToRoute('app_accueil');
    }
    /***************************************************/
    /*                Création d'un compte
    /***************************************************/
    #[Route('/createAccount', name: 'createAccount')]
    public function createAccountAction(): Response
    {

        return $this->render('Vue/Account/createAccount.html.twig');
    }

    /***************************************************/
    /*                Edition d'un compte
    /***************************************************/
    #[Route('/editProfile', name: 'editProfile')]
    public function editProfileAction(): Response
    {

        return $this->render('Vue/Account/editProfile.html.twig');
    }

}
