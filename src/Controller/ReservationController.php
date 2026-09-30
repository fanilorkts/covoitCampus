<?php

namespace App\Controller;

use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route ('/Trajet/{id_trajet}/reserver', name: 'app_reservation_new_for_trajet', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserver (\App\Entity\Trajets $trajet, Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
            if (!$this->IsCsrfTokenValid('reserver'.$trajet->getId(), $request->getPayload()->getString('_token'))) {
                throw $this->createAccessDeniedException();
            }

            $user = $this->getUser();

            if ($trajet->getStatut() !== Trajets::STATUTS_OUVERT) {
                $this->addFlash('error', 'Le trajet n\'est pas ouvert à la réservation.');
                return $this->redirectToRoute('app_trajets_index');
            }else if ($trajet->getIdConducteur() === $user) {
                $this->addFlash('error', 'Vous ne pouvez pas réserver votre propre trajet.');
                return $this->redirectToRoute('app_trajets_index');
            }else if ($trajet-> $reservationRepository->findOneBy(['id_trajet' => $trajet, 'id_passager' => $user])) {
                $this->addFlash('error', 'Vous avez déjà réservé ce trajet.');
                return $this->redirectToRoute('app_trajets_index');
            }else {
                $reservation = new Reservation();
                $reservation->setIdTrajet($trajet);
                $reservation->setIdPassager($user);
                $reservation->setStatut(Reservation::STATUS_EN_ATTENTE);
                $reservation->setDateReservation(new \DateTime());

                $entityManager->persist($reservation);
                $entityManager->flush();

                $conducteur = $trajet->getIdConducteur();

                $this->addFlash('success', 'Votre réservation a été envoyer à ' . $conducteur->getNom() . ' !');
                return $this->redirectToRoute('app_trajets_index');
        }
    }

    #[Route('/{id}/accepter', name: 'app_reservation_accepter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function accepter (Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $trajet = $reservation->getIdTrajet();

        if ($trajet->getIdConducteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatut() !== Reservation::STATUS_EN_ATTENTE) {
            $this->addFlash('error', 'Nous attendons la réponse du conducteur pour cette réservation.');
            return $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
        }
        try {
            $trajet->reserverPlace($reservation->getIdPassager());
            $reservation->setStatut(Reservation::STATUS_CONFIRME);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été acceptée !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible d\'accepter la réservation : ' . $e->getMessage());
        }

        return  $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
    }

    #[Route('/{id}/refuser', name: 'app_reservation_refuser', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function refuser (Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $trajet = $reservation->getIdTrajet();

        if ($trajet->getIdConducteur() !== $this->getUser() || $this->IsCsrfTokenValid('refuser'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatut() !== Reservation::STATUS_EN_ATTENTE) {
            $reservation->setStatut(Reservation::STATUS_REFUSEE);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a été refusée !');
        }

        return  $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
    }




    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
