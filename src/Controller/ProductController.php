<?php

namespace App\Controller;

use App\Entity\Order;
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
        //On recupére tout les produits de la BD et on les envoie à la vue
        $productsrepository = $em->getRepository(Produit::class);
        $products = $productsrepository->findAll();
        return $this->render("Vue/Product/productList.html.twig", ['produits' => $products]);
    }

    /***************************************************/
    /*      Gérer l'ajout au panier
    /***************************************************/
    #[Route('/AddporductOrder/{id}', name: 'AddporductOrder')]
    public function AddporductOrderAction(int $id,Request $request,EntityManagerInterface $em): Response
    {
        $productRepository = $em->getRepository(Produit::class);
        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);

        // Récupération de l'utilisateur actuel (ici j'ai mis "simon" comme login à modifier plus tard pour pas que ça
        // soit en dur)
        $client = $userRepository->findOneBy(['login' => 'simon']);

        // Récupération du produit en fonction de l'ID
        $produit = $productRepository->findOneBy(['id' => $id]);

        // Si le produit n'existe pas, on redirige vers la liste des produits
        if (!$produit) {
            return $this->redirectToRoute('product_Listproduct');
        }

        // Si le formulaire a été soumis
        if ($request->isMethod('POST')) {
            // Récupération de la quantité commandée dans le formulaire
            $quantite = $request->request->get('quantite');

            // Vérification de la validité de la quantité commandée
            if ($quantite <= 0 || $quantite > $produit->getQuantite()) {
                $this->addFlash('error', 'Quantité invalide');
            } else {
                // Vérification si une commande pour ce produit existe déjà pour l'utilisateur actuel
                $order = $orderRepository->findOneBy(['client' => $client, 'produit' => $produit]);

                // Si une commande existe déjà, on met à jour la quantité commandée
                if ($order) {
                    $order->setQuantite($order->getQuantite() + $quantite);
                } else {
                    // Sinon, on crée une nouvelle commande
                    $order = new Order();
                    $order->setClient($client);
                    $order->setProduit($produit);
                    $order->setQuantite($quantite);
                    $em->persist($order);
                }

                // Mise à jour de la quantité en stock du produit
                $produit->setQuantite($produit->getQuantite() - $quantite);

                // Enregistrement des modifications dans la base de données
                $em->flush();

                // Ajout d'un message de confirmation à la page
                $this->addFlash('success', 'Produit ajouté au Panier');
            }
        }
        return $this->redirectToRoute('product_Orders');
    }

    /***************************************************/
    /*          L'affichage du panier
    /***************************************************/
    #[Route('/Orders', name: 'Orders')]
    public function OrderAction(EntityManagerInterface $em): Response
    {
        // Récupération de l'utilisateur connecté
        /*
        /** @var User $client */
       // $client = $this->getUser();

        //on le fait en dure pour le moment
        $userRepository = $em->getRepository(User::class);
        $client = $userRepository->findOneBy(['login' => 'simon']);

        // Récupération de tous les produits dans son panier
        $produits = $client->getOrders();

        // Affichage de la vue du panier
        return $this->render('Vue/Product/orderList.html.twig', ['produits' => $produits]);
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