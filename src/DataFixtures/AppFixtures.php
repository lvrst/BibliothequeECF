<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = FakerFactory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        // Cette fixture fait partie du groupe "test".
        // Cela permet de cibler seulement certains fixtures
        // quand on exécute la commande doctrine:fixtures:load.
        // Pour que la méthode statique getGroups() soit prise
        // en compte, il faut que la classe implémente
        // l'interface FixtureGroupInterface.
        return ['test'];
    }

    public function load(ObjectManager $manager)
    {
        $livresCount = 1000;
        $auteursCount = 500;
        $emprunteursCount = 100;
        $empruntsCount = 200;

        // $product = new Product();
        // $manager->persist($product);
        $this->loadAdmin($manager);
        $auteurs = $this->loadAuteurs($manager, $auteursCount);
        $emprunteurs = $this->loadEmprunteurs($manager, $emprunteursCount);
        $genres = $this->loadGenres($manager);
        $livres = $this->loadLivres($manager, $livresCount, $auteurs, $genres);
        $emprunts = $this->loadEmprunts($manager, $empruntsCount, $livres, $emprunteurs);

        $manager->flush();
    }

    public function loadAdmin(ObjectManager $manager)
    {
        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('admin@example.net');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
    }


    public function loadGenres(ObjectManager $manager)
    {
        $genres = [];

        $genreList = array('poésie', 'nouvelle', 'roman historique', 'roman d\'amour', 'roman d\'aventure', 'science-fiction', 'fantasy', 'biographie', 'conte', 'témoignage', 'théâtre', 'essai', 'journal intime');
        
        foreach ($genreList as $genreItem) {
            $genre = new Genre();
            $genre->setNom($genreItem);
            $manager->persist($genre);
            $genres[]=$genre;
        }

    return $genres;
    } 

    public function loadLivres(ObjectManager $manager, int $livresCount, array $auteurs, array $genres)
    {
        // création d'un tableau qui contiendra les livres qu'on va créer
        // la fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objects puissent utiliser les livres
        $livres = [];

        // création d'un livre avec des données constantes
        $livre = new Livre();
        $livre->setTitre('Lorem ipsum dolor sit amet');
        $livre->setAnneeEdition(2010);
        $livre->setNombrePages(100);
        $livre->setCodeIsbn('9785786930024');
        $livre->setAuteur($auteurs[0]);
        $livre->addGenre($genres[0]);

        $manager->persist($livre);

        // on ajoute le premier livre créé
        $livres[] = $livre;

        // second livre
        $livre = new Livre();
        $livre->setTitre('Consectetur adipiscing elit');
        $livre->setAnneeEdition(2011);
        $livre->setNombrePages(150);
        $livre->setCodeIsbn('9783817260935');
        $livre->setAuteur($auteurs[1]);
        $livre->addGenre($genres[1]);
        $manager->persist($livre);
        $livres[] = $livre;

        // troisième livre
        $livre = new Livre();
        $livre->setTitre('Mihi quidem Antiochum');
        $livre->setAnneeEdition(2012);
        $livre->setNombrePages(200);
        $livre->setCodeIsbn('9782020493727');
        $livre->setAuteur($auteurs[2]);
        $livre->addGenre($genres[2]);
        $manager->persist($livre);
        $livres[] = $livre;

        // quatrième livre
        $livre = new Livre();
        $livre->setTitre('Quem audis satis belle');
        $livre->setAnneeEdition(2013);
        $livre->setNombrePages(250);
        $livre->setCodeIsbn('9794059561353');
        $livre->setAuteur($auteurs[3]);
        $livre->addGenre($genres[3]);
        $manager->persist($livre);
        $livres[] = $livre;

        for ($i = 0; $i < $livresCount; $i++) {
            $livre = new Livre();
            $livre->setTitre($this->faker->sentence(4));
            $livre->setAnneeEdition($this->faker->year());
            $livre->setNombrePages($this->faker->numberBetween(100, 900));
            $livre->setCodeIsbn($this->faker->isbn13());
            $auteurIndex = array_rand($auteurs, 1);
            $livre->setAuteur($auteurs[$auteurIndex]);
            $genreIndex = array_rand($genres, 1);
            $livre->addGenre($genres[$genreIndex]);
            if ($i%3==2) {
                $genreIndex = array_rand($genres, 1);
                $livre->addGenre($genres[$genreIndex]);
            }
            $manager->persist($livre);
            $livres[] = $livre;
        }

        $manager->flush();
        return $livres;
    }

    public function loadEmprunteurs(ObjectManager $manager, int $emprunteursCount)
    {
        // création d'un tableau qui contiendra les emprunteurs qu'on va créer
        // la fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objects puissent utiliser les emrpunteurs
        $emprunteurs = [];

        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('foo.foo@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        $manager->persist($user);
        
        // création d'un premier emprunteur avec des données constantes
        $emprunteur = new Emprunteur();
        $emprunteur->setNom('foo');
        $emprunteur->setPrenom('foo');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 10:00:00'));
        $emprunteur->setUser($user);
        $manager->persist($emprunteur);
        // on ajoute le premier emprunteur créé
        $emprunteurs[] = $emprunteur;

        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('bar.bar@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        $manager->persist($user);
        
        // création d'un second emprunteur avec des données constantes
        $emprunteur = new Emprunteur();
        $emprunteur->setNom('bar');
        $emprunteur->setPrenom('bar');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(false);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 11:00:00'));
        $emprunteur->setUser($user);
        $manager->persist($emprunteur);
        // on ajoute le second emprunteur créé
        $emprunteurs[] = $emprunteur;

        // création d'un user avec des données constantes
        // ici il s'agit du compte admin
        $user = new User();
        $user->setEmail('baz.baz@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_EMPRUNTEUR']);

        $manager->persist($user);
        
        // création d'un troisième emprunteur avec des données constantes
        $emprunteur = new Emprunteur();
        $emprunteur->setNom('baz');
        $emprunteur->setPrenom('baz');
        $emprunteur->setTel('123456789');
        $emprunteur->setActif(true);
        $emprunteur->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 12:00:00'));
        $emprunteur->setUser($user);
        $manager->persist($emprunteur);
        // on ajoute le troisième emprunteur créé
        $emprunteurs[] = $emprunteur;

        for ($i = 0; $i < $emprunteursCount; $i++) {

            // création d'un user avec des données constantes
            // ici il s'agit du compte admin
            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_EMPRUNTEUR']);
    
            $manager->persist($user);
            $emprunteur = new Emprunteur();
            $emprunteur->setNom($this->faker->lastname());
            $emprunteur->setPrenom($this->faker->firstname());
            $emprunteur->setTel($this->faker->phoneNumber());
            $emprunteur->setActif($this->faker->boolean());
            $emprunteur->setDateCreation($this->faker->dateTime()); 
            $emprunteur->setUser($user);
            $manager->persist($emprunteur);
            // on ajoute le troisième emprunteur créé
            $emprunteurs[] = $emprunteur;
        }

        $manager->flush();
        return $emprunteurs;
    }


    public function loadEmprunts(ObjectManager $manager, int $empruntsCount, array $livres, array $emprunteurs)
    {
        // création d'un tableau qui contiendra les emprunts qu'on va créer
        // la fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objects puissent utiliser les emrpunts
        $emprunts = [];
        
        // Utiliser le premier livre
        $livreIndex = 0;
        // Récupération d'un livre précisé par l'index $livreIndex
        $livre = $livres[$livreIndex];


        // création d'un premier emprunt avec des données constantes
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-01 10:00:00'));
        $emprunt->setDateRetour(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-03-01 10:00:00'));
        // attention voir pour livre_id à insérer dans chaque
        $emprunt->setLivre($livre);
        $emprunt->setEmprunteur($emprunteurs[0]);
        $manager->persist($emprunt);
        // on ajoute le premier emprunt créé
        $emprunts[] = $emprunt;

        $livreIndex++;
        // Récupération d'un livre précisé par l'index $livreIndex
        $livre = $livres[$livreIndex];
    
        // création d'un second emprunt avec des données constantes
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-03-01 10:00:00'));
        $emprunt->setDateRetour(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-04-01 10:00:00'));
        // attention voir pour livre_id à insérer dans chaque
        $emprunt->setLivre($livre);
        $emprunt->setEmprunteur($emprunteurs[1]);
        $manager->persist($emprunt);
        // on ajoute le second emprunt créé
        $emprunts[] = $emprunt;

        $livreIndex++;
        // Récupération d'un livre précisé par l'index $livreIndex
        $livre = $livres[$livreIndex];
    
        // création d'un troisième emprunt avec des données constantes
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-04-01 10:00:00'));
        // attention voir pour livre_id à insérer dans chaque
        $emprunt->setLivre($livre);
        $emprunt->setEmprunteur($emprunteurs[2]);
        $manager->persist($emprunt);
        // on ajoute le troisième emprunt créé
        $emprunts[] = $emprunt;


        for ($i = 0; $i < $empruntsCount; $i++) {
            // création d'un emprunt avec des données constantes
            $emprunt = new Emprunt();
            $emprunt->setDateEmprunt($this->faker->dateTimeThisDecade());
            $dateEmprunt = $emprunt->getDateEmprunt();
            $dateRetour = \DateTime::createFromFormat('Y-m-d H:i:s', $dateEmprunt->format('Y-m-d H:i:s'));
            $dateRetour->add(new \DateInterval('P1M'));
            $livreIndex = array_rand($livres, 1);
            $emprunt->setLivre($livres[$livreIndex]);
            $emprunt->setDateRetour($dateRetour);
            //$emprunt->setLivre($livre);
            $emprunteurIndex = array_rand($emprunteurs, 1);
            $emprunt->setEmprunteur($emprunteurs[$emprunteurIndex]);
            $manager->persist($emprunt);
            // on ajoute le premier emprunteur créé
            $emprunts[] = $emprunt;
        }
    
    
        $manager->flush();
        return $emprunts;
    }

    public function loadAuteurs(ObjectManager $manager, int $auteursCount)
    {
        // création d'un tableau qui contiendra les auteurs qu'on va créer
        // la fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objects puissent utiliser les auteurs
        $auteurs = [];

        $nomAuteurList = array(
                'auteur inconnu' => ' ',
                'Cartier' => 'Hugues',
                'Lambert' => 'Armand',
                'Moitessier' => 'Thomas'
        );
        
        foreach ($nomAuteurList as $nom => $prenom) {
            $auteur = new Auteur();
            $auteur->setNom($nom);
            $manager->persist($prenom);
            $auteurs[]=$auteur;
        }

        // création d'auteurs avec des données aléatoires
        for ($i = 0; $i < $auteursCount; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($this->faker->lastname());
            $auteur->setPrenom($this->faker->firstname());

            $manager->persist($auteur);

            // on ajoute chaque auteur créé
            $auteurs[] = $auteur;
        }

        $manager->flush();
        return $auteurs;
    }
}