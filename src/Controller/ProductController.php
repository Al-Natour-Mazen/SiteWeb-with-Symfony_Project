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
    public function Createproduct(EntityManagerInterface $em , Request $requete): Response
    {
        // creation de du nouveau Produit
        $TheNewProduct = new Produit();

        $form =  $this->createForm(ProductType::class,$TheNewProduct);
        $form->add('send',SubmitType::class,['label' =>'Créer Votre Produit']);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($TheNewProduct);
            $em->flush();
            $this->addFlash('info','Le produit a été créer !');
        }

        $args=array(
            'myform' => $form->createView(),
        );
        return $this->render("Vue/Product/CreateProduct.html.twig",$args);
    }



    /***************************************************/
    /*       Afficher les Listes Des Produits Dispo
    /***************************************************/
    #[Route('/Listproduct', name: 'Listproduct')]
    public function productListAction(): Response
    {

        $orders=null;
        return $this->render("Vue/Product/productList.html.twig", ['orders' => $orders]);
    }
}



/* produit list
 *
<!--   <div id="PorductList">
    <h2>Liste des salles disponibles à acheter dans notre magasin</h2>
    <form action="{{ path('product_productList') }}" method="post">
        <table>
            <tr>
                <th>Label</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Choix</th>
            </tr>
            {% for order in orders %}
                <tr>
                    <td>{{ order.product.label }}</td>
                    <td>{{ order.product.price }}</td>
                    <td>{{ order.product.quantity }}</td>
                    <td>
                        {% if order.product.quantity > 0 %}
                            <label>
                                <select name="{{ order.product.id }}">
                                    {% for i in 0..(order.product.quantity) %}
                                        <option value="{{ i }}">{{ i }}</option>
                                    {% endfor %}
                                </select>
                            </label>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        </table>
        <input id="BtnModifier" type="submit" value="Modifier">
    </form>
</div>
#}}
--->*/