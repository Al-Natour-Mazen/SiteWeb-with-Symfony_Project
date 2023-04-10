<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{
    /***************************************************/
    /*       Creer Un Produit que par un Admin
    /***************************************************/
    #[Route('/createproduct', name: 'createproduct')]
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
    #[Route('/listproduct', name: 'listproduct')]
    #[IsGranted('ROLE_CLIENT')]
    public function productListAction(EntityManagerInterface $em): Response
    {
        //on recupere le client déjà connecte
        $client = $this->getUser();
        // on recupere ses commandes
        $orders = $client->getOrders();
        //On recupére tout les produits de la BD et on les envoie à la vue
        $productsrepository = $em->getRepository(Produit::class);
        $products = $productsrepository->findAll();
        return $this->render("Vue/Product/productList.html.twig", ['produits' => $products, 'panier' => $orders]);
    }


    /***************************************************/
    /*  Envoie d'un mail contenant le nbr de produits dispo
    /***************************************************/
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/mailing', name: 'mail')]
    #[IsGranted('ROLE_CLIENT')]
    public function mailAction(Request $request, MailerInterface $mailer, EntityManagerInterface $em): ?Response
    {
        if($request->isMethod('POST')){
            //on recupere le mail de l'utilisateur
            $theemail = $request->request->get('mail');
            //on verifie que c'est pas un mail vide
            if($theemail === ""){
                $this->addFlash('info', 'Veuillez entrez un mail valide !');
                return $this->redirectToRoute('product_mail');
            }

            //on recupere les produits du magasin
            $productsrepository = $em->getRepository(Produit::class);
            $produits = $productsrepository->findAll();

            //on compte le nombre de produits total
            $nbproductmagasin = count($produits);
            //on va compte le nombre de produits dispo à l achat
            $nbproductmagasinDispo = 0;
            foreach ($produits as $produit){
                if($produit->getQuantite() > 0 ){
                    $nbproductmagasinDispo ++;
                }
            }

            // on creer le mail pour l'envoyer avec le corps, sujet, etc ...
            $email = (new Email())
                ->from('H4ImmoShop@H4Shop.com')
                ->to($theemail)
                ->subject('Nombre De Produits dans le Magasin')
                ->text("Bonjour,\n   Nous avons " . $nbproductmagasin . " produit(s) dans notre magasin dont " . $nbproductmagasinDispo . " qui sont disponbiles à l'achat. 
                            \nCordialement,\nLa Direction du H4ImmoShop.")
                ;

            //on envoie le mail
            $mailer->send($email);
            // on informe l'utilisateur
            $this->addFlash('info', 'Mail envoye avec succes :)');
            // on redergie vers la liste des produits
            return $this->redirectToRoute('product_listproduct');
        }
        return $this->render('Vue/Product/mailnbproduct.html.twig');
    }
}