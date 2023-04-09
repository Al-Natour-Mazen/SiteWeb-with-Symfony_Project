<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/order', name: 'order_')]
#[IsGranted('ROLE_CLIENT')]
class OrderController extends AbstractController
{
    /***************************************************/
    /*      Gérer l'ajout au panier
    /***************************************************/
    #[Route('', name: 'addproduct')]
    public function addProductToCartAction(Request $request, EntityManagerInterface $em)
    {
        // Récupération de l'utilisateur actuel
        $client = $this->getUser();
        //on récupère l'id du produit
        $id = $request->request->get('id');
        // Récupération du produit en fonction de l'ID
        $productRepository = $em->getRepository(Produit::class);
        $produit = $productRepository->findOneBy(['id' => $id]);
        // Si le produit n'existe pas, on redirige vers la liste des produits
        if ($produit === null) {
            return $this->redirectToRoute('product_listproduct');
        } else {
            // Récupération de la quantité commandée dans le formulaire
            $quantite = $request->request->get('quantite');
            // Vérification si une commande pour ce produit existe déjà pour l'utilisateur actuel
            $orderRepository = $em->getRepository(Order::class);
            $order = $orderRepository->findOneBy(['client' => $client, 'produit' => $produit]);
            $quantiteDejaCommande = 0;
            if ($order !== null) {
                $quantiteDejaCommande = $order->getQuantite();
            }
            // Vérification de la validité de la quantité commandée
            if ($quantite < $quantiteDejaCommande * -1 || $quantite > $produit->getQuantite()) {
                $this->addFlash('info', 'Quantité invalide');
            }if ($quantite == 0) {
                // Si la quantité est nulle, on ne fait rien
                $this->addFlash('info' , 'Vous ne pouvez pas ajouter une quantite egale a zero');
                return $this->redirectToRoute('product_listproduct');
            } else {
                // Si une commande existe déjà, on met à jour la quantité commandée
                if ($order !== null) {
                    $newQuantite = $quantiteDejaCommande + $quantite;
                    if ($newQuantite === 0) {
                        $em->remove($order);
                    } else {
                        $order->setQuantite($newQuantite);
                    }
                } else {
                    // Sinon, on crée une nouvelle commande
                    $neworder = new Order();
                    $neworder->setClient($client);
                    $neworder->setProduit($produit);
                    $neworder->setQuantite($quantite);
                    $em->persist($neworder);
                }
                // Mise à jour de la quantité en stock du produit
                $produit->setQuantite($produit->getQuantite() - $quantite);
                // Enregistrement des modifications dans la base de données
                $em->flush();
                // Ajout d'un message de confirmation à la page
                if($quantite > 0 )
                    $this->addFlash('info', 'Produit ajouté au panier avec succès !');
                else
                    $this->addFlash('info', 'Produit retiré du panier !');
            }
        }
        return $this->redirectToRoute('order_clientcart');
    }

    /***************************************************/
    /*          L'affichage du panier
    /***************************************************/
    #[Route('/clientcart', name: 'clientcart')]
    public function ClientCartAction(): Response
    {
        //on recupere le client connecte
        $client = $this->getUser();
        // Récupération de tous les produits dans son panier
        $produits = $client->getOrders();
        // Affichage de la vue du panier
        return $this->render('Vue/Order/orderList.html.twig', ['produits' => $produits]);
    }


    /***************************************************/
    /*          Vider completement le panier
    /***************************************************/
    #[Route('/clearcart', name: 'clearcart')]
    public function clearCartAction(EntityManagerInterface $em): Response
    {
        // On Récurpere le Client actuellement connecte
        $client = $this->getUser();
        // On cherche tout les orders liées au client
        $orderRepository = $em->getRepository(Order::class);
        $orders = $orderRepository->findBy(['client' => $client]);
        if($orders){
            // On vide tout les orders liés à ce client
            foreach ($orders as $order) {
                //On re recupere le produit pour le remttre dans la BD
                $product = $order->getProduit();
                if ($product) {
                    $product->setQuantite($product->getQuantite() + $order->getQuantite());
                }
                //On enleve l'order
                $em->remove($order);
            }
            // On sauvegarde les changment
            $em->flush();
            // On ajoute un msg flash pour informé
            $this->addFlash('info',"Votre Panier a été vider avec succès !");
        }else{
            $this->addFlash('info',"Votre Panier n'a pas été vider, Un probléme est survenue !");
        }
        return $this->redirectToRoute('order_clientcart');
    }

    /***************************************************/
    /*        Suppresion d'un seul Produit du Panier
    /***************************************************/
    #[Route('/removeproductfromcart/{productId}',
        name: 'removeproductfromcart',
        requirements: ['productId' => '[1-9]\d*']
    )]
    public function removeProductFromCartAction(int $productId, EntityManagerInterface $em): Response
    {
        // On Récurpere le Client actuellement connecte
        $client = $this->getUser();

        // on cherche le produit à enlever pour verifier si ce produit existe
        $productRepository = $em->getRepository(Produit::class);
        $product = $productRepository->find($productId);

        if ($product) {
            // on cherche l'order precis pour ce client et ce produit
            $orderRepository = $em->getRepository(Order::class);
            $order = $orderRepository->findOneBy(['client' => $client, 'produit' => $product]);

            if ($order) {
                // on MAJ la quantite dans la BD
                $product->setQuantite($product->getQuantite() + $order->getQuantite());
                // On enleve l'order
                $em->remove($order);
                // On enregistre les modifs dans la BD
                $em->flush();
                // On ajoute un msg flash pour informé
                $this->addFlash('info', "Le produit a été retiré de votre panier avec succès !");
            } else {
                // On ajoute un msg flash pour informé
                $this->addFlash('info', "Le produit n a pas été trouvé dans votre panier !");
            }
        } else {
            // On ajoute un msg flash pour informé
            $this->addFlash('info', "Le produit specifie n existe pas.");
        }
        return $this->redirectToRoute('order_clientcart');
    }


    /***************************************************/
    /*        Commander les produits
    /***************************************************/
    #[Route('/placeorder', name: 'placeorder')]
    public function placeOrderAction(EntityManagerInterface $em): Response
    {
        // On récupère le client actuellement connecté
        $client = $this->getUser();
        // On cherche tous les orders liés au client
        $orderRepository = $em->getRepository(Order::class);
        $orders = $orderRepository->findBy(['client' => $client]);
        if($orders){
            // On vide tous les orders liés à ce client
            foreach ($orders as $order) {
                //On enlève l'order de la BD
                $em->remove($order);
            }
            // On sauvegarde les changements
            $em->flush();
            // On ajoute un msg flash pour informé
            $this->addFlash('info',"Votre Commande a été passé, Merci pour votre Confiance !");
        }
        else{
            $this->addFlash('info',"Votre Commande n'est pas passé, Un probléme est survenue !");
        }
        return $this->redirectToRoute('order_clientcart');
    }

}




