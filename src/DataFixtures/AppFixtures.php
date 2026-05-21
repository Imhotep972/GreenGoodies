<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setPrix(24.99);
        $product->setName("Kit d'hygiène recyclable ");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Pour une salle de bain éco-friendly");
        $product->setPhoto("produit_1.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(4.50);
        $product->setName("Shot Tropical");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Fruits frais, pressés à froid");
        $product->setPhoto("produit_2.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(16.90);
        $product->setName("Gourde en bois");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("50cl, bois d’olivier");
        $product->setPhoto("produit_3.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(19.90);
        $product->setName("Disques Démaquillants x3");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Solution efficace pour vous démaquiller en douceur ");
        $product->setPhoto("produit_4.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(32);
        $product->setName("Bougie Lavande & Patchouli");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Cire naturelle");
        $product->setPhoto("produit_5.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(5.4);
        $product->setName("Brosse à dent");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Bois de hêtre rouge issu de forêts gérées durablement");
        $product->setPhoto("produit_6.png");
        $manager->persist($product);


        $product = new Product();
        $product->setPrix(12.30);
        $product->setName("Kit couvert en bois");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Revêtement Bio en olivier & sac de transport");
        $product->setPhoto("produit_7.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(8.50);
        $product->setName("Nécessaire, déodorant Bio");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("50ml déodorant à l’eucalyptus");
        $product->setPhoto("produit_8.png");
        $manager->persist($product);

        $product = new Product();
        $product->setPrix(18.9);
        $product->setName("Savon Bio");
        $product->setDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortdesc("Thé, Orange & Girofle");
        $product->setPhoto("produit_9.png");
        $manager->persist($product);

    
        $manager->flush();
    }
}
