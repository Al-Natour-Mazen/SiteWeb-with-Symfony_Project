<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\CheckPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;
use Symfony\Component\Form\FormTypeInterface;

#[Route('/account', name: 'account_')]
class AccountController extends AbstractController
{
    /***************************************************/
    /*                Connexion d'un compte
    /***************************************************/
    #[Route('/connect', name: 'connect')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Vue/Account/connect.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /***************************************************/
    /*                Déconnexion d'un compte
    /***************************************************/
    #[Route(path: '/disconnect', name: 'disconnect')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /***************************************************/
    /*                Création d'un compte
    /***************************************************/
    #[Route('/createAccount', name: 'createAccount')]
    public function createAccountAction(EntityManagerInterface $em ,
                                        UserPasswordHasherInterface $passwordHasher,
                                        Request $requete,
                                        CheckPassword $checkPassword
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }
        // creation de la nouvelle personne
        $TheNewOne = new User();

        $form =  $this->createForm(UserType::class,$TheNewOne);
        $form->add('send',SubmitType::class,['label' =>'Créer mon compte']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $TheNewOne->setRoles(['ROLE_CLIENT']);

            if (!$checkPassword->check($TheNewOne->getPassword())) {
                $this->addFlash('info','Votre mot de passe n\'est pas valide (service)');
                return $this->redirectToRoute('account_createAccount');
            }
            else {
                $hashedPassword = $passwordHasher->hashPassword($TheNewOne, $TheNewOne->getPassword());
                $TheNewOne->setPassword($hashedPassword);

                $em->persist($TheNewOne);
                $em->flush();
                $this->addFlash('info', 'Votre compte Client a été créer !');
                return $this->redirectToRoute('app_accueil');
            }
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
    public function editProfileAction(EntityManagerInterface $em,
                                      UserPasswordHasherInterface $passwordHasher,
                                      Request $requete): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class,$user);
        $form->add('modify',SubmitType::class,['label' => 'modifier votre profile']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $em->flush();
            $this->addFlash('info','Votre compte a été modifier !');
            if($this->isGranted('ROLE_CLIENT'))
                return $this->redirectToRoute('product_Listproduct');
            else if ($this->isGranted('ROLE_SUPERADMIN' ))
                return $this->redirectToRoute('app_accueil');
        }

        $args=array(
            'myform' => $form->createView(),
        );

        return $this->render('Vue/Account/editProfile.html.twig',$args);
    }
}
