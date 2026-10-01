<?php

namespace App\Controller;

use App\Entity\Trajets;
use App\Form\TrajetsType;
use App\Repository\ReservationRepository;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trajets')]
final class TrajetsController extends AbstractController
{
    #[Route(name: 'app_trajets_index', methods: ['GET'])]
    public function index(Request $request, TrajetsRepository $trajetsRepository): Response
    {
        $dateSaisie = $request->query->get('date_saisie');
        $date = $dateSaisie ? new \DateTime($dateSaisie) : null;

        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajetsRepository->RechercheTrajets(
                $request->query->get('origine'),
                $request->query->get('destination'),
                $date
            ),
        ]);
    }

    #[Route('/new', name: 'app_trajets_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trajet = new Trajets();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setIdConducteur($this->getUser());
            $trajet->setPlacesRestantes($trajet->getPlacesTotales());
            $trajet->setStatut(Trajets::STATUTS_OUVERT);

            $entityManager->persist($trajet);
            $entityManager->flush();

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/new.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/mes-trajets', name: 'app_trajets_mes_trajets', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesTrajets(TrajetsRepository $trajetsRepository): Response
    {
        return $this->render('trajets/mes_trajets.html.twig', [
            'trajets' => $trajetsRepository->findBy(
                ['id_conducteur' => $this->getUser()],
                ['date_heure' => 'DESC']
            ),
        ]);
    }

    #[Route('/{id}', name: 'app_trajets_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Trajets $trajet, ReservationRepository $reservationRepository): Response
    {
        return $this->render('trajets/show.html.twig', [
            'trajet' => $trajet,
            'reservations' => $reservationRepository->findBy(['id_trajet' => $trajet]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trajets_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Trajets $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($trajet->getIdConducteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trajets_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trajets $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($trajet->getIdConducteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $trajet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trajet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }
}