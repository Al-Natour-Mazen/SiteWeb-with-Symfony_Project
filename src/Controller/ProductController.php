<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{
    /***************************************************/
    /*       Afficher les Listes Des Produits Dispo
    /***************************************************/
    #[Route('/productList', name: 'productList')]
    public function productListAction(): Response
    {

        $orders=null;
        return $this->render("Vue/Product/productList.html.twig", ['orders' => $orders]);
    }
}
