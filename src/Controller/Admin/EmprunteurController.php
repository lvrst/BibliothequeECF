<?php

namespace App\Controller\Admin;

use App\Entity\Emprunteur;
use App\Form\EmprunteurType;
use App\Repository\EmprunteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/emprunteur")
 */
class EmprunteurController extends AbstractController
{
    /**
     * @Route("/new", name="admin_emprunteur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $emprunteur = new Emprunteur();
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la présence d'un mot de passe
            // On ne met à jour le mot de passe que si l'utilisateur
            // en a fourni un.
            if ($form->get('plainPassword')->getData()) {
                // Hashage du mot de passe
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($emprunteur);
            $entityManager->flush();

            return $this->redirectToRoute('emprunteur_index');
        }

        return $this->render('emprunteur/new.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form->createView(),
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

    /**
     * @Route("/{id}/edit", name="admin_emprunteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Emprunteur $emprunteur): Response
    {
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('emprunteur_index');
        }

        return $this->render('emprunteur/edit.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_emprunteur_delete", methods={"POST"})
     */
    public function delete(Request $request, Emprunteur $emprunteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunteur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($emprunteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('emprunteur_index');
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
