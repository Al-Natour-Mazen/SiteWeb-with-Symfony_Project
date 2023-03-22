<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Produit;
use App\Entity\User;
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
    #[Route('', name: 'addPorduct')]
    public function addProductToCart(Request $request, EntityManagerInterface $em)
    {

        $productRepository = $em->getRepository(Produit::class);
        $orderRepository = $em->getRepository(Order::class);

        // Récupération de l'utilisateur actuel
        $client = $this->getUser();

        //on récupère l'id du produit
        $id = $request->request->get('id');

        // Récupération du produit en fonction de l'ID
        $produit = $productRepository->findOneBy(['id' => $id]);

        // Si le produit n'existe pas, on redirige vers la liste des produits
        if (!$produit) {
            return $this->redirectToRoute('product_Listproduct');
        } else {
            // Récupération de la quantité commandée dans le formulaire
            $quantite = $request->request->get('quantite');

            // Vérification si une commande pour ce produit existe déjà pour l'utilisateur actuel
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
                return $this->redirectToRoute('product_Listproduct');
            } else {
                // Si une commande existe déjà, on met à jour la quantité commandée
                if ($order) {
                    $newQuantite = $order->getQuantite() + $quantite;
                    if ($newQuantite === 0) {
                        $em->remove($order);
                    } else {
                        $order->setQuantite($newQuantite);
                        $em->persist($order);
                    }
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
                $this->addFlash('info', 'Produit ajouté au panier avec succès !');
            }
        }
        return $this->redirectToRoute('order_ClientCart');
    }



    /*public function addPorductAction(Request $request,EntityManagerInterface $em): Response
    {
        // Si le formulaire a été soumis
        if ($request->isMethod('POST')) {

            $productRepository = $em->getRepository(Produit::class);
            $orderRepository = $em->getRepository(Order::class);

            // Récupération de l'utilisateur actuel
            $client = $this->getUser();

            //on recupere l'id du produit
            $id = $request->request->get('id');

            // Récupération du produit en fonction de l'ID
            $produit = $productRepository->findOneBy(['id' => $id]);

            // Si le produit n'existe pas, on redirige vers la liste des produits
            if (!$produit) {
                return $this->redirectToRoute('product_Listproduct');
            }
            else{
                // Récupération de la quantité commandée dans le formulaire
                $quantite = $request->request->get('quantite');

                // Vérification si une commande pour ce produit existe déjà pour l'utilisateur actuel
                $order = $orderRepository->findOneBy(['client' => $client, 'produit' => $produit]);
                $quantiteDejaCommande = 0;
                if($order !== null)
                    $quantiteDejaCommande =  $order->getQuantite();

                // Vérification de la validité de la quantité commandée
                if ($quantite < $quantiteDejaCommande || $quantite > $produit->getQuantite()) {
                    $this->addFlash('error', 'Quantité invalide');
                } else if ($quantite > 0){
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
                    $this->addFlash('info', 'Produit ajouté au panier avec succès !');
                }
                else if ($quantite < 0 ){
                    $em->remove($order);
                    $produit->setQuantite($produit->getQuantite() + $quantite);

                    // Enregistrement des modifications dans la base de données
                    $em->flush();

                    // Ajout d'un message de confirmation à la page
                    $this->addFlash('info', 'Quntite retiré de votre panier !');
                }
            }
        }
        return $this->redirectToRoute('order_ClientCart');
    }*/

    /***************************************************/
    /*          L'affichage du panier
    /***************************************************/
    #[Route('/ClientCart', name: 'ClientCart')]
    public function ClientCartAction(EntityManagerInterface $em): Response
    {
        $client = $this->getUser();

        // Récupération de tous les produits dans son panier
            $produits = $client->getOrders();


        // Affichage de la vue du panier
        return $this->render('Vue/Order/orderList.html.twig', ['produits' => $produits]);
    }


    /***************************************************/
    /*          Vider completement le panier
    /***************************************************/
    #[Route('/clearCart', name: 'clearCart')]
    public function clearCartAction(EntityManagerInterface $em): Response
    {
        $orderRepository = $em->getRepository(Order::class);

        // On Récurpere le Client actuellement connecte
        $client = $this->getUser();

        // On cherche tout les orders liées au client
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

            $this->addFlash('info',"Votre Panier a été vider avec succès !");
        }else{
            $this->addFlash('info',"Votre Panier n'a pas été vider, Un probléme est survenue !");
        }


        return $this->redirectToRoute('order_ClientCart');
    }

    /***************************************************/
    /*        Suppresion d'un seul Produit du Panier
    /***************************************************/
    #[Route('/removeProductFromCart/{productId}', name: 'removeProductFromCart')]
    public function removeProductFromCartAction(int $productId, EntityManagerInterface $em): Response
    {
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Produit::class);

        // On Récurpere le Client actuellement connecte
        // On le fait en dur pour le moment
        $client = $this->getUser();

        // on cherche le produit à enlever pour verifier si ce produit existe
        $product = $productRepository->find($productId);

        if ($product) {
            // on cherche l'order precis pour ce client et ce produit
            $order = $orderRepository->findOneBy(['client' => $client, 'produit' => $product]);

            if ($order) {
                // on MAJ la quantite dans la BD
                if ($product) {
                    $product->setQuantite($product->getQuantite() + $order->getQuantite());
                }

                // On enleve l'order
                $em->remove($order);

                // On enregistre les modifs dans la BD
                $em->flush();

                // On ajoute un msg flash pour informé
                $this->addFlash('info', "Le produit a été retiré de votre panier avec succès !");
            } else {
                // On ajoute un msg flash pour informé
                $this->addFlash('info', "Le produit n'a pas été trouvé dans votre panier.");
            }
        } else {
            // On ajoute un msg flash pour informé
            $this->addFlash('info', "Le produit spécifié n'existe pas.");
        }

        return $this->redirectToRoute('order_ClientCart');
    }


    /***************************************************/
    /*        Commander les produits
    /***************************************************/
    #[Route('/placeOrder', name: 'placeOrder')]
    public function placeOrderAction(EntityManagerInterface $em): Response
    {
        $orderRepository = $em->getRepository(Order::class);

        // On récupère le client actuellement connecté
        // On le fait en dur pour le moment
        $client = $this->getUser();

        // On cherche tous les orders liés au client
        $orders = $orderRepository->findBy(['client' => $client]);

        if($orders){
            // On vide tous les orders liés à ce client
            foreach ($orders as $order) {
                //On enlève l'order de la BD
                $em->remove($order);
            }

            // On sauvegarde les changements
            $em->flush();


            $this->addFlash('info',"Votre Commande a été passé, Merci pour votre Confiance !");
        }
        else{
            $this->addFlash('info',"Votre Commande n'est pas passé, Un probléme est survenue !");
        }

        return $this->redirectToRoute('order_ClientCart');
    }
}
