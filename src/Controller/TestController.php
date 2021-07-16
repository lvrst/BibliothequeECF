<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\LivreRepository;
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
        LivreRepository $livreRepository): Response
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
        $livre = $userRepository->find(1);
        dump($livre);

        // Récupération des livres dont le titre contient le mot clé `lorem`
        // La chaîne de caractères qu'on veut rechercher dans le titre des livres.
        $titre = 'lorem';
        // Le repository renvoit tous les livres qui contiennent la
        // chaîne de caractères recherchée dans le titre.
        $livres = $livreRepository->findByTitre($titre);
        dump($livres);

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
