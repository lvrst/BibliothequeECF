<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use App\Repository\EmprunteurRepository;
use App\Repository\EmpruntRepository;
use App\Entity\Livre;
use App\Entity\Genre;
use App\Entity\Emprunt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(
        UserRepository $userRepository,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        GenreRepository $genreRepository,
        EmprunteurRepository $emprunteurRepository,
        EmpruntRepository $empruntRepository): Response
    {
        // Récupération de l'entity manager.
        $entityManager = $this->getDoctrine()->getManager();

        // Utilisateurs
        // Requêtes de lecture 
        // Récupération de la liste complète des users.
        $users = $userRepository->findAll();
        dump($users);

        // Récupération du user dont l'id est 1.
        $user = $userRepository->find(1);
        dump($user);

        // Récupération du user dont l'email est celui indiqué.
        $foo = $userRepository->findOneByEmail(`foo.foo@example.com`);
        dump($foo);

        // Récupération de la liste complète des users qui ont le rôle ROLE_EMPRUNTEUR.
        $emprunteurRoles = $userRepository->findByRoles('ROLE_EMPRUNTEUR');
        dump($emprunteurRoles);

        // Livres 
        // Requêtes de lecture
        // Récupération de la liste complète des livres.
        $livres = $livreRepository->findAll();
        dump($livres);

        // Récupération du livre dont l'id est 1. 
        $livre = $livreRepository->find(1);
        dump($livre);

        // Récupération des livres dont le titre contient le mot clé `lorem`
        // La chaîne de caractères qu'on veut rechercher dans le titre des livres.
        $titre = 'lorem';
        // Le repository renvoie tous les livres qui contiennent la
        // chaîne de caractères recherchée dans le titre.
        $livres = $livreRepository->findByTitre($titre);
        dump($livres);

        // Récupération des livres dont l'id de l'auteur est 2.
        $id = 2;
        $livres = $livreRepository->findByAuteurId($id);
        dump($livres);

        // Récupération de la liste des livres dont le genre contient le mot clé `roman`
        $genre = "roman";
        $livres = $livreRepository->findByGenre($genre);
        dump($livres);

        // Requêtes de création
        // Ajouter un nouveau livre
        $auteur = $auteurRepository->findOneById(2);
        $genre = $genreRepository->findOneById(6);
        $livre = new Livre();
        $livre->setTitre('Totum autem id externum');
        $livre->setAnneeEdition(2020);
        $livre->setNombrePages(300);
        $livre->setCodeIsbn('9790412882714');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);
        $entityManager->flush();
        dump($livre);

        // Requête de mise à jour
        // Modification du livre dont l'id est 2. 
        $genre = $genreRepository->findOneById(5);
        $livre = $livreRepository->find(2);
        $livre->setTitre('Aperiendum est igitur');
        $livre->addGenre($genre);
        $entityManager->flush();
        dump($livre);

        // Requête de suppression
        $livre = $livreRepository->findOneById(123);
        dump($livre);
        $entityManager->remove($livre);
        $entityManager->flush();
        dump($livre);

        // Emprunteurs
        // Récupération de la liste complète des emprunteurs. 
        $emprunteurs = $emprunteurRepository->findAll();
        dump($emprunteurs);

        // Récupération de l'emprunteur dont l'id est 3.
        $id = 3;
        $emprunteur = $emprunteurRepository->find($id);
        dump($emprunteur);

        // Récupération des données de l'emprunteur qui est relié au user dont l'id est 3.
        $id = 3;
        $emprunteurArr = $emprunteurRepository->findByUserId($id);
        $emprunteur = $emprunteurArr[0];
        dump($emprunteur);

        // Récupération de la liste des emprunteurs dont le nom ou le prénom contient le mot-clé 'foo'.
        $name = 'foo';
        $emprunteurs = $emprunteurRepository->findByFirstnameOrLastname($name);
        dump($emprunteurs);

        // Récupération de la liste des emprunteurs dont le téléphone contient le mot-clé '1234'.
        $num = '1234';
        $emprunteurs = $emprunteurRepository->findByTel($num);
        dump($emprunteurs);

        // Récupération de la liste des emprunteurs dont la date de création est antérieure au 01/03/2021 exclus.
        $date = '2021-01-03';
        $emprunteurs = $emprunteurRepository->findByDateCreation($date);
        dump($emprunteurs);

        // Récupération de la liste des emprunteurs inactifs
        $activity = false;
        $emprunteurs = $emprunteurRepository->findActivity($activity);
        dump($emprunteurs);

        // Emprunts
        // Requêtes de lecture
        // Récupération de la liste des 10 derniers emprunts au niveau chronologique.
        $num = 10;
        $emprunts = $empruntRepository->findLastEmprunts($num);
        dump($emprunts);

        // Récupération de la liste des emprunts de l'emprunteur dont l'id est 2.
        $id = 2;
        $emprunts = $empruntRepository->findByEmprunteurId($id);
        dump($emprunts);

        // Récupération de la liste des emprunts du livre dont l'id est 3. 
        $id = 3;
        $emprunts = $empruntRepository->findByLivreId($id);
        dump($emprunts);

        // Récupération de la liste des emprunts retournés avant le O1/O1/2021. 
        $date = '2021-01-01';
        $emprunts = $empruntRepository->findByReturnedBefore($date);
        dump($emprunts);

        // Récupération de la liste des emprunts qui n'ont pas encore été retournés (date de retour nulle).
        $emprunts = $empruntRepository->findByUnreturned();
        dump($emprunts);

        // Récupération des données de l'emprunt du livre dont l'id est 3 et qui n'a pas encore été retourné.
        $id = 3;
        $empruntArr = $empruntRepository->findByIdUnreturned($id);
        $emprunt = $empruntArr[0];
        dump($emprunt);

        // Requête de création
        // Ajouter un nouvel emprunt.
        $livre = $livreRepository->findOneById(1);
        $emprunteur = $emprunteurRepository->findOneById(1);
        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-12-01 16:00:00'));
        $emprunt->setLivre($livre);
        $emprunt->setEmprunteur($emprunteur);
        $entityManager->flush();
        dump($emprunt);

        // Requête de mise à jour
        // Modifier l'emprunt dont l'id est 3.
        $emprunt = $empruntRepository->findOneById(3);
        $emprunt->setDateRetour(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-05-01 10:00:00'));
        $entityManager->flush();
        dump($emprunt);

        // Requête de suppression
        // Supprimer l'emprunt dont l'id est 42.
        $id = 42;
        $emprunt = $empruntRepository->findOneById($id);
        dump($emprunt);
        $entityManager->remove($emprunt);
        $entityManager->flush();
        dump($emprunt);



        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
