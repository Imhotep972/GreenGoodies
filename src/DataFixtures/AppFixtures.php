<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{           
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setPrice(2499);
        $product->setName("Kit d'hygiène recyclable ");
        $product->setFullDescription("Ce kit d’hygiène recyclable réunit l’essentiel pour une routine plus naturelle et respectueuse de l’environnement. Chaque accessoire est conçu pour réduire les déchets tout en offrant une expérience de soin agréable et efficace au quotidien.
Les matériaux durables et doux permettent un usage confortable, que ce soit pour nettoyer, exfolier ou prendre soin de la peau. Leur qualité assure une utilisation régulière sans compromis sur le bien‑être ni sur la performance.
Pratique et esthétique, ce kit s’intègre facilement dans une salle de bain éco‑friendly. Léger, réutilisable et pensé pour durer, il accompagne chaque geste beauté vers une routine plus responsable et plus consciente.");
        $product->setShortDescription("Pour une salle de bain éco-friendly");
        $product->setPicture("produit_1.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(450);
        $product->setName("Shot Tropical");
        $product->setFullDescription("Ce shot énergisant associe la douceur de la mangue à la fraîcheur de l’ananas pour offrir un concentré naturel de vitamines. Grâce à la pression à froid, chaque gorgée préserve l’intégralité des nutriments, pour une boisson pure et intensément revitalisante.
Riche en enzymes digestives et en antioxydants, l’ananas aide à stimuler la digestion tandis que la mangue apporte une énergie douce et durable. Ensemble, ces fruits créent une synergie idéale pour soutenir l’immunité et réveiller le métabolisme naturellement.
Avec sa texture fluide et son goût tropical vibrant, ce shot devient un geste bien‑être simple et quotidien. Léger, naturel et sans additifs, il hydrate, dynamise et apporte un véritable coup de soleil à la journée.");
        $product->setShortDescription("Fruits frais, pressés à froid");
        $product->setPicture("produit_2.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(1690);
        $product->setName("Gourde en bois");
        $product->setFullDescription("Cette gourde élégante associe la chaleur du bois d’olivier à une conception moderne pensée pour un usage quotidien. Chaque pièce est unique grâce à ses veinures naturelles, offrant un design authentique et intemporel.
Le bois d’olivier, reconnu pour sa résistance et ses propriétés naturellement antibactériennes, garantit une utilisation durable et hygiénique. Sa structure robuste et son isolation interne préservent la fraîcheur de vos boissons plus longtemps.
Pensée pour réduire l’usage du plastique, cette gourde devient un geste éco‑responsable au quotidien. Agréable en main et durable, elle allie style, praticité et engagement pour un mode de vie plus respectueux de l’environnement.");
        $product->setShortDescription("50cl, bois d’olivier");
        $product->setPicture("produit_3.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(1990);
        $product->setName("Disques Démaquillants x3");
        $product->setFullDescription("Ces disques réutilisables offrent une alternative douce et écologique aux cotons jetables. Leur texture moelleuse glisse facilement sur la peau pour un démaquillage efficace sans irritation.
Fabriqués à partir de fibres douces et durables, ils capturent maquillage, impuretés et excès de sébum en un seul geste. Leur structure absorbante optimise l’utilisation de vos produits démaquillants tout en respectant l’équilibre naturel de la peau.
Lavables et conçus pour durer, ces disques s’intègrent facilement dans une routine beauté responsable. Légers, pratiques et agréables à utiliser, ils deviennent rapidement un indispensable pour un soin quotidien plus sain et plus respectueux de l’environnement.");
        $product->setShortDescription("Solution efficace pour vous démaquiller en douceur ");
        $product->setPicture("produit_4.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(3200);
        $product->setName("Bougie Lavande & Patchouli");
        $product->setFullDescription("Cette bougie associe la douceur apaisante de la lavande aux notes profondes et boisées du patchouli. Sa cire naturelle diffuse une lumière chaleureuse et une fragrance délicate, idéale pour instaurer une atmosphère calme et réconfortante.
Grâce à son parfum équilibré, elle aide à détendre l’esprit tout en purifiant subtilement l’air ambiant. La lavande apporte une sensation de sérénité, tandis que le patchouli enveloppe la pièce d’une présence plus chaleureuse et enveloppante.
Conçue pour offrir une combustion propre et durable, cette bougie devient un véritable rituel bien‑être. Élégante, naturelle et agréable au quotidien, elle transforme chaque moment en parenthèse de douceur.");
        $product->setShortDescription("Cire naturelle");
        $product->setPicture("produit_5.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(540);
        $product->setName("Brosse à dent");
        $product->setFullDescription("Cette brosse à dents en bois de hêtre rouge offre une alternative naturelle, élégante et durable aux modèles en plastique. Sa forme ergonomique et sa texture douce assurent une prise en main confortable pour un brossage agréable au quotidien.
Fabriquée à partir de bois issu de forêts gérées durablement, elle allie respect de l’environnement et durabilité. Ses poils doux nettoient efficacement tout en préservant les gencives sensibles.
Solide, esthétique et éco‑responsable, elle s’intègre parfaitement dans une routine d’hygiène plus consciente. Réduire les déchets devient un geste simple, tout en apportant une touche naturelle et chaleureuse à la salle de bain.");
        $product->setShortDescription("Bois de hêtre rouge issu de forêts gérées durablement");
        $product->setPicture("produit_6.webp");
        $manager->persist($product);


        $product = new Product();
        $product->setPrice(1230);
        $product->setName("Kit couvert en bois");
        $product->setFullDescription("Ce kit de couverts en bois offre une alternative naturelle et élégante aux ustensiles jetables. Leur revêtement bio en olivier apporte une texture douce et agréable, idéale pour savourer chaque repas avec plus d’authenticité.
Solides et durables, ces couverts accompagnent aussi bien les repas du quotidien que les sorties en extérieur. Leur finition lisse assure un confort d’utilisation tout en respectant la sensibilité du palais.
Livré avec un sac de transport pratique, ce kit s’intègre parfaitement dans une démarche zéro déchet. Léger, réutilisable et esthétique, il permet d’adopter un mode de vie plus responsable tout en gardant ses essentiels toujours à portée de main.");
        $product->setShortDescription("Revêtement Bio en olivier & sac de transport");
        $product->setPicture("produit_7.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(850);
        $product->setName("Nécessaire, déodorant Bio");
        $product->setFullDescription("Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.");
        $product->setShortDescription("50ml déodorant à l’eucalyptus");
        $product->setPicture("produit_8.webp");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(1890);
        $product->setName("Savon Bio");
        $product->setFullDescription("Ce savon bio associe la chaleur du thé aux notes fraîches d’orange et à la profondeur du girofle. Sa mousse douce nettoie la peau en respectant son équilibre naturel, tout en laissant un parfum subtil, réconfortant et légèrement épicé qui accompagne agréablement chaque utilisation.
Formulé avec des ingrédients naturels, il offre une expérience sensorielle agréable au quotidien. Ses arômes fruités et épicés apportent une touche d’énergie tout en enveloppant la peau d’une sensation de douceur.
Idéal pour une routine plus responsable, ce savon solide réduit les déchets tout en offrant un soin authentique et durable. Pratique, parfumé et agréable à utiliser, il s’intègre facilement dans une salle de bain éco‑friendly et accompagne chaque geste beauté vers plus de naturel.");
        $product->setShortDescription("Thé, Orange & Girofle");
        $product->setPicture("produit_9.webp");
        $manager->persist($product);
    
        $manager->flush();

        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable('2026-05-18 12:00:00'));
        $user->setEmail('master.imhotep@gmail.com');
        $user->setPrenom('Master');
        $user->setNom('Imhotep');
        $user->setPassword('12345678');
        $user->setApiEnabled(true);
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $manager->persist($user);

        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable('2026-05-10 12:00:00'));
        $user->setEmail('jean.veuplus@gmail.com');
        $user->setPrenom('Jean');
        $user->setNom('Veuplus');
        $user->setPassword('12345678');
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $manager->persist($user);

        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable('2026-05-18 12:00:00'));
        $user->setEmail('jean.cerien@gmail.com');
        $user->setPrenom('Jean');
        $user->setNom('Cérien');
        $user->setArchive(true);
        $user->setDeletedAt(new \DateTimeImmutable('2026-06-18 12:00:00'));
        $user->setPassword('12345678');
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $manager->persist($user);

        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable('2026-06-18 12:00:00'));
        $user->setEmail('jean.peuplu@gmail.com');
        $user->setPrenom('Jean');
        $user->setNom('Peuplu');
        $user->setApiEnabled(true);
        $user->setPassword('12345678');
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $manager->persist($user);

        $manager->flush();

    }
}
