<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{
    /***************************************************/
    /*       Creer Un Produit que par un Admin
    /***************************************************/
    #[Route('/Createproduct', name: 'Createproduct')]
    public function CreateproductAction(EntityManagerInterface $em , Request $requete): Response
    {
        // creation de du nouveau Produit
        $TheNewProduct = new Produit();

        //On creer le Formulaire
        $form =  $this->createForm(ProductType::class,$TheNewProduct);
        $form->add('send',SubmitType::class,['label' =>'Créer Votre Produit']);
        $form->handleRequest($requete);

        //On verifie le formulaire
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($TheNewProduct);
            $em->flush();
            $this->addFlash('info','Le produit a été créer !');
        }

        $args=array(
            'myform' => $form->createView(),
        );
        return $this->render("/Vue/Product/createProduct.html.twig",$args);
    }

    /***************************************************/
    /*       Afficher les Listes Des Produits Dispo
    /***************************************************/
    #[Route('/Listproduct', name: 'Listproduct')]
    public function productListAction(EntityManagerInterface $em): Response
    {
        $productsrepository = $em->getRepository(Produit::class);
        $products = $productsrepository->findAll();
        return $this->render("Vue/Product/productList.html.twig", ['produits' => $products]);
    }
}
