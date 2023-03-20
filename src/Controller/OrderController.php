<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/order', name: 'order_')]
class OrderController extends AbstractController
{
    /***************************************************/
    /*      Gérer l'ajout au panier
    /***************************************************/
    #[Route('', name: 'AddPorduct')]
    public function AddPorductAction(Request $request,EntityManagerInterface $em): Response
    {
        // Si le formulaire a été soumis
        if ($request->isMethod('POST')) {

            $productRepository = $em->getRepository(Produit::class);
            $userRepository = $em->getRepository(User::class);
            $orderRepository = $em->getRepository(Order::class);

            // Récupération de l'utilisateur actuel (ici j'ai mis "simon" comme login à modifier plus tard pour pas que ça
            // soit en dur)
            $client = $userRepository->findOneBy(['login' => 'maz12']);

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
                    $this->addFlash('info', 'Produit ajouté au Panier');
                }
            }
        }
        return $this->redirectToRoute('order_ClientOrders');
    }

    /***************************************************/
    /*          L'affichage du panier
    /***************************************************/
    #[Route('/ClientOrders', name: 'ClientOrders')]
    public function OrderDisplayAction(EntityManagerInterface $em): Response
    {
        // Récupération de l'utilisateur connecté
        /*
        /** @var User $client */
        // $client = $this->getUser();

        //on le fait en dure pour le moment
        $userRepository = $em->getRepository(User::class);
        $client = $userRepository->findOneBy(['login' => 'maz12']);

        // Récupération de tous les produits dans son panier
            $produits = $client->getOrders();


        // Affichage de la vue du panier
        return $this->render('Vue/Product/orderList.html.twig', ['produits' => $produits]);
    }


    /***************************************************/
    /*          Vidage du panier
    /***************************************************/
    #[Route('/EmptyOrders', name: 'EmptyOrders')]
    public function EmptyOrdersAction(EntityManagerInterface $em): Response
    {

        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);

        // On Récurpere le Client actuellement connecte
        // On le fait en dur pour le moment
        $client = $userRepository->findOneBy(['login' => 'maz12']);
        $IdClient = $client->getId();

        // On cherche tout les orders liées au client
        $orders = $orderRepository->findBy(['client' => $IdClient]);

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

        return $this->redirectToRoute('order_ClientOrders');
    }

    /***************************************************/
    /*        Suppresion d'un seul Produit du Panier
    /***************************************************/
    #[Route('/EmptyOrderProduct/{id}', name: 'EmptyOrderProduct')]
    public function EmptyOrderProductAction(int $id,EntityManagerInterface $em): Response
    {
        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);

        // On Récurpere le Client actuellement connecte
        // On le fait en dur pour le moment
        $client = $userRepository->findOneBy(['login' => 'maz12']);
        $IdClient = $client->getId();

        // On cherche tout les orders liées au client
        $orders = $orderRepository->findBy(['client' => $IdClient]);

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

        return $this->redirectToRoute('product_Orders');
    }




}
