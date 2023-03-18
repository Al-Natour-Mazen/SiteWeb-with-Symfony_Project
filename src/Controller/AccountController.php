<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;
use Symfony\Component\Form\FormTypeInterface;

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
    public function createAccountAction(EntityManagerInterface $em , Request $requete): Response
    {
        // creation de la nouvelle personne
        $TheNewOne = new User();

        $form =  $this->createForm(UserType::class,$TheNewOne);
        $form->add('send',SubmitType::class,['label' =>'Créer mon compte']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $TheNewOne->setRoles(['CLIENT']);
            $em->persist($TheNewOne);
            $em->flush();
            $this->addFlash('info','Votre compte Client a été créer !');
            return $this->redirectToRoute('app_accueil');
        }

        $args=array(
            'myform' => $form->createView(),
        );

        return $this->render('Vue/Account/createAccount.html.twig', $args);
    }

    /***************************************************/
    /*                Edition d'un compte
    /***************************************************/
    #[Route('/editProfile',
        name: 'editProfile',
    )]
    public function editProfileAction(EntityManagerInterface $em , Request $requete): Response
    {
        $login = "malnatou"; // on le fait en dur pour le moment quand on aura l'auth on recupere l'utilisateur connecte

        $userrepository = $em->getRepository(User::class);
        $user = $userrepository->findOneBy(['login' => $login]);

        if(is_null($user))
            throw new NotFoundHttpException('Ce Client avec ce Login : ' . $login . ' n\'existe pas');

        $form = $this->createForm(UserType::class,$user);
        $form->add('modify',SubmitType::class,['label' => 'modifier votre profile']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('info','Votre compte a été modifier !');
            if($user->getRoles()[0] === "ROLE_CLIENT")
                return $this->redirectToRoute('product_Listproduct');
            else if ($user->getRoles()[0] === "ROLE_SUPERADMIN" )
                return $this->redirectToRoute('app_accueil');
        }

        $args=array(
            'myform' => $form->createView(),
        );

        return $this->render('Vue/Account/editProfile.html.twig',$args);
    }

}
