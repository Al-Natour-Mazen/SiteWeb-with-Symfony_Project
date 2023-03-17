<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VenteFixtures extends Fixture
{
    public function load(ObjectManager $em): void
    {
        // Les produits (salles du bâtiment H04)

        $produitAmphi = new Produit();
        $produitAmphi
            ->setLibelle('Amphi BE')
            ->setPrixUnitaire(135000)
            ->setQuantite(0);
        $em->persist($produitAmphi);

        $produit02 = new Produit();
        $produit02
            ->setLibelle('Salle BE02')
            ->setPrixUnitaire(15000)
            ->setQuantite(1);
        $em->persist($produit02);

        $produit11 = new Produit();
        $produit11
            ->setLibelle('Salle BE11')
            ->setPrixUnitaire(15000)
            ->setQuantite(1);
        $em->persist($produit11);

        $produit13 = new Produit();
        $produit13
            ->setLibelle('Salle BE13')
            ->setPrixUnitaire(10000)
            ->setQuantite(1);
        $em->persist($produit13);

        $produit14 = new Produit();
        $produit14
            ->setLibelle('Salle BE14')
            ->setPrixUnitaire(9000)
            ->setQuantite(1);
        $em->persist($produit14);

        $produit15 = new Produit();
        $produit15
            ->setLibelle('Salle BE15')
            ->setPrixUnitaire(18000)
            ->setQuantite(0);
        $em->persist($produit15);

        $produit18 = new Produit();
        $produit18
            ->setLibelle('Salle BE18')
            ->setPrixUnitaire(15000)
            ->setQuantite(1);
        $em->persist($produit18);

        $produit19 = new Produit();
        $produit19
            ->setLibelle('Salle BE19')
            ->setPrixUnitaire(9000)
            ->setQuantite(0);
        $em->persist($produit19);

        $produitDemande = new Produit();
        $produitDemande
            ->setLibelle('Salle sur demande')
            ->setPrixUnitaire(1)
            ->setQuantite(982);
        $em->persist($produitDemande);



        // Les utilisateurs

        $userSAdmin = new User();
        $userSAdmin
            ->setLogin('sadmin')
            ->setPassword('nimdas')
            ->setRoles(array('ROLE_SUPERADMIN'))
            ->setNom('Super')
            ->setPrenom('Man')
            ->setDateNaissance(\DateTime::createFromFormat('Y-m-d', "2023-03-15"));
        $em->persist($userSAdmin);

        $userGilles = new User();
        $userGilles
            ->setLogin('gilles')
            ->setPassword('sellig')
            ->setRoles(array('ROLE_ADMIN'))
            ->setNom('Subrenat')
            ->setPrenom('Gilles')
            ->setDateNaissance(new \DateTime());     // on préfère éviter de faire chuter notre note
        $em->persist($userGilles);

        $userRita = new User();
        $userRita
            ->setLogin('rita')
            ->setPassword('atir')
            ->setRoles(array('ROLE_CLIENT'))
            ->setNom('Zrour')
            ->setPrenom('Rita')
            ->setDateNaissance(new \DateTime());    // toujours pas
        $em->persist($userRita);

        $userSimon = new User();
        $userSimon
            ->setLogin('simon')
            ->setPassword('nomis')
            ->setRoles(array('ROLE_CLIENT'))
            ->setNom('Un')
            ->setPrenom('Simon')
            ->setDateNaissance(\DateTime::createFromFormat('Y-m-d', "1980-02-29"));
        $em->persist($userSimon);



        // Quelques commandes

        $order1 = new Order();
        $order1
            ->setProduit($produitAmphi)
            ->setClient($userSimon)
            ->setQuantite(1);
        $em->persist($order1);
        $produitAmphi->addOrder($order1);
        $userSimon->addOrder($order1);

        $order2 = new Order();
        $order2
            ->setProduit($produit15)
            ->setClient($userSimon)
            ->setQuantite(1);
        $em->persist($order2);
        $produit15->addOrder($order2);
        $userSimon->addOrder($order2);

        $order3 = new Order();
        $order3
            ->setProduit($produit19)
            ->setClient($userRita)
            ->setQuantite(1);
        $em->persist($order3);
        $produit19->addOrder($order3);
        $userRita->addOrder($order3);

        $order4 = new Order();
        $order4
            ->setProduit($produitDemande)
            ->setClient($userRita)
            ->setQuantite(17);
        $em->persist($order4);
        $produitDemande->addOrder($order4);
        $produitDemande->addOrder($order4);

        $em->flush();

    }
}
