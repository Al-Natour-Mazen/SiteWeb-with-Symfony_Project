<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    /***************************************************/
    /* Création d'un Admin Dispo que pour Un SuperAdmin
    /***************************************************/
    #[Route('/createAdmin', name: 'createAdmin')]
    public function createAdminAction(EntityManagerInterface $em,
                                      UserPasswordHasherInterface $passwordHasher,
                                      Request $requete): Response
    {
        // il faut verifier si l'utilisateur est un superadmin sinon pas le droit d'acceder ici

        // creation de la nouvelle personne
        $NewAdmin = new User();

        $form =  $this->createForm(UserType::class,$NewAdmin);
        $form->add('send',SubmitType::class,['label' =>'Ajouter un Admin']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $NewAdmin->setRoles(['ROLE_ADMIN']);

            $hashedPassword = $passwordHasher->hashPassword($NewAdmin, $NewAdmin->getPassword());
            $NewAdmin->setPassword($hashedPassword);

            $em->persist($NewAdmin);
            $em->flush();
            $this->addFlash('info','L\'ajout de l\'admin a été effectue !');
            return $this->redirectToRoute('app_accueil');
        }

        $args=array(
            'myform' => $form->createView(),
        );

        return $this->render('/Vue/Admin/createAdmin.html.twig', $args);
    }
}
