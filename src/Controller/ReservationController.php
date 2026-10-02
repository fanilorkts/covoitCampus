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
    #[IsGranted('ROLE_ADMIN')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route ('/Trajet/{id}/reserver', name: 'app_reservation_new_trajet', methods: ['POST'])]
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
            }
            
            if ($trajet->getIdConducteur() === $user) {
                $this->addFlash('error', 'Vous ne pouvez pas réserver votre propre trajet.');
                return $this->redirectToRoute('app_trajets_index');
            }
            
            if ($reservationRepository->findOneBy(['id_trajet' => $trajet, 'id_passager' => $user])) {
                $this->addFlash('error', 'Vous avez déjà réservé ce trajet.');
                return $this->redirectToRoute('app_trajets_index');
            }
            
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

    #[Route('/{id}/accepter', name: 'app_reservation_accepter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function accepter (Reservation $reservation, EntityManagerInterface $entityManager, Request $request): Response
    {
        $trajet = $reservation->getIdTrajet();

        if ($trajet->getIdConducteur() !== $this->getUser() || !$this->IsCsrfTokenValid('accepter'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatut() !== Reservation::STATUS_EN_ATTENTE) {
            $this->addFlash('error', 'Oups ! Cette réservation a déjà été traitée.');
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
    public function refuser (Reservation $reservation, EntityManagerInterface $entityManager, Request $request): Response
    {
        $trajet = $reservation->getIdTrajet();

        if ($trajet->getIdConducteur() !== $this->getUser() || !$this->IsCsrfTokenValid('refuser'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatut() === Reservation::STATUS_EN_ATTENTE) {
            $reservation->setStatut(Reservation::STATUS_REFUSEE);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a été refusée !');
        }

        return  $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
    }

    #[Route('/mes-reservations', 'app_reservation_mes_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesReservations (ReservationRepository $reservationRepository): Response
    {
      return $this->render('reservation/mes_reservations.html.twig', [
          'reservations' => $reservationRepository->findBy(['id_passager' => $this->getUser()], ['date_reservation' => 'DESC']),
      ]);
    }

    #[Route('/{id}/annuler', name: 'app_reservation_annuler', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function annuler(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($reservation->getIdPassager() !== $this->getUser()
            || !$this->isCsrfTokenValid('annuler'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $trajet = $reservation->getIdTrajet();

        if ($trajet->getStatut() === Trajets::STATUTS_TERMINE) {
            $this->addFlash('error', 'Ce trajet est terminé, impossible d\'annuler.');
            return $this->redirectToRoute('app_reservation_mes_reservations');
        }

        if ($reservation->getStatut() === Reservation::STATUS_CONFIRME) {
            $trajet->libererPlace();
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été annulée.');
        return $this->redirectToRoute('app_reservation_mes_reservations');
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Reservation $reservation): Response
    {
        $user = $this->getUser();
        if ($reservation->getIdPassager() !== $user
            && $reservation->getIdTrajet()->getIdConducteur() !== $user) {
            throw $this->createAccessDeniedException();
    }
    return $this->render('reservation/show.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
