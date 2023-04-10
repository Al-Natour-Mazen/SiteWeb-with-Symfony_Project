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
    public function mailAction(Request $request, MailerInterface $mailer): ?Response
    {
        if($request->isMethod('POST')){

            $theemail = $request->request->get('mail');

            $email = (new Email())
                ->from('H4ImmoShop@H4Shop.com')
                ->to($theemail)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
            $this->addFlash('info', 'Le mail a ete envoye, checkez votre boite mail :)');
           // return $this->redirectToRoute('product_listproduct');
        }
        return $this->render('Vue/Product/mailnbproduct.html.twig');
    }

}