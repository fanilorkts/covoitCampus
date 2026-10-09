<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Security\Voter\VehiculeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vehicules')]
#[IsGranted('ROLE_USER')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(#[CurrentUser] Utilisateurs $user, VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findDuProprietaire($user),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] Utilisateurs $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = (new Vehicule())->setProprietaire($user);
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vehicule);
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule ajouté.');

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted(VehiculeVoter::EDIT, subject: 'vehicule')]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule mis à jour.');

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted(VehiculeVoter::DELETE, subject: 'vehicule')]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_vehicule'.$vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule supprimé.');
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}
