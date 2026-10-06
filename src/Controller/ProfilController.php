<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\ProfilType;
use App\Repository\AvisRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(
        Utilisateurs $utilisateur,
        AvisRepository $avisRepository,
        TrajetsRepository $trajetsRepository,
        ReservationRepository $reservationRepository
    ): Response {
        return $this->render('profil/show.html.twig', [
            'utilisateur' => $utilisateur,
            'note' => $avisRepository->noteMoyenne($utilisateur->getId()),
            'nbTrajets' => $trajetsRepository->countConducteur($utilisateur)
                + $reservationRepository->countPassager($utilisateur),
            'avis' => $avisRepository->findBy(['id_cible' => $utilisateur], ['date' => 'DESC']),
        ]);
    }

    #[Route('/mon-profil/modifier', name: 'app_profil_modifier', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur instanceof Utilisateurs) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('app_profil', ['id' => $utilisateur->getId()]);
        }

        return $this->render('profil/modifier.html.twig', ['form' => $form]);
    }
}