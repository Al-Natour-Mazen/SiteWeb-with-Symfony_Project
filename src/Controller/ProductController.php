<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{
    /***************************************************/
    /*       Creer Un Produit que par un Admin
    /***************************************************/
    #[Route('/Createproduct', name: 'Createproduct')]
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_CLIENT')]
    public function productListAction(EntityManagerInterface $em): Response
    {
        //On recupére tout les produits de la BD et on les envoie à la vue
        $productsrepository = $em->getRepository(Produit::class);
        $products = $productsrepository->findAll();
        return $this->render("Vue/Product/productList.html.twig", ['produits' => $products]);
    }

}


/*
 * test à laisser pour le moment
  #[Route('/product/{id}', name: 'product')]
  public function productAction(int $id,Request $request,EntityManagerInterface $em): ?Response
  {
      $quantite = $request->request->get('quantite');
      $productsrepository = $em->getRepository(Produit::class);
      $product = $productsrepository->findOneBy(['id' => $id]);
      dump($product);
      dump($quantite);
      return  new Response('<body>Hello World!</body>');
  }*/