<?php

namespace App\Controller;

use App\Entity\Emprunteur;
use App\Form\EmprunteurType;
use App\Repository\EmprunteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/emprunteur")
 */
class EmprunteurController extends AbstractController
{
    /**
     * @Route("/", name="emprunteur_index", methods={"GET"})
     */
    public function index(EmprunteurRepository $emprunteurRepository): Response
    {
        return $this->render('emprunteur/index.html.twig', [
            'emprunteurs' => $emprunteurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="emprunteur_show", methods={"GET"})
     */
    public function show(Emprunteur $emprunteur): Response
    {
        return $this->render('emprunteur/show.html.twig', [
            'emprunteur' => $emprunteur,
        ]);
    }

    private function redirectUser(string $route, Emprunteur $emprunteur, EmprunteurRepository $emrpunteurRepository)
    {
        // Récupération du compte de l'utilisateur qui est connecté
        $user = $this->getUser();

        // On vérifie si l'utilisateur est un emprunteur 
        if (in_array('ROLE_EMPRUNTEUR', $user->getRoles())) {
            // Récupèration du profil student
            $userEmprunteur = $emprunteurRepository->findOneByUser($user);

            // Comparaison du profil demandé par l'utilisateur et le profil de l'utilisateur
            // Si les deux sont différents, on redirige l'utilisateur vers la page de son profil
            if ($emprunteur->getId() != $userEmprunteur->getId()) {
                return $this->redirectToRoute($route, [
                    'id' => $userEmprunteur->getId(),
                ]);
            }
        }

        // Si aucune redirection n'est nécessaire, on renvoit une valeur nulle
        return null;
    }
}
