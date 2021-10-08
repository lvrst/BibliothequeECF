<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Form\EmpruntType;
use App\Repository\LivreRepository;
use App\Repository\EmpruntRepository;
use App\Repository\EmprunteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/emprunt")
 */
class EmpruntController extends AbstractController
{
    /**
     * @Route("/", name="emprunt_index", methods={"GET"})
     */
    public function index(EmpruntRepository $empruntRepository, EmprunteurRepository $emprunteurRepository): Response
    {
        $emprunts = $empruntRepository->findAll();

        if ($this->isGranted('ROLE_EMPRUNTEUR')) {
            $user = $this->getUser();
            $emprunteur = $emprunteurRepository->findOneByUser($user);
            $emprunts = $emprunteur->getEmprunts();
        }

        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $emprunts,
        ]);
    }

    /**
     * @Route("/{id}", name="emprunt_show", methods={"GET"})
     */
    public function show(Emprunt $emprunt, EmprunteurRepository $emprunteurRepository, LivreRepository $livreRepository): Response
    {
        if($this->isGranted('ROLE_EMPRUNTEUR')) {
            $user = $this->getUser();
            $emprunteur = $emprunteurRepository->findOneByUser($user);
            if (!$emprunteur->getEmprunts()->contains($emprunt)){
                throw new NotFoundHttpException();
            }
        }
        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
            'livre' => $emprunt->getLivre()
        ]);
    }
}
