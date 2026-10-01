<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{

    public const STATUS_EN_ATTENTE = 'En attente';
    public const STATUS_CONFIRME = 'Confirmée';
    public const STATUS_REFUSEE = 'Refusée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_reservation = null;

    #[ORM\ManyToOne (targetEntity: Trajets::class)]
    #[ORM\JoinColumn(name: "id_trajet", referencedColumnName: "id")]
    private ?Trajets $id_trajet = null;

    #[ORM\ManyToOne (targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "id_passager", referencedColumnName: "id")]
    private ?Utilisateurs $id_passager = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateReservation(): ?\DateTime
    {
        return $this->date_reservation;
    }

    public function setDateReservation(\DateTime $date_reservation): static
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getIdTrajet(): ?Trajets
    {
        return $this->id_trajet;
    }

    public function setIdTrajet(Trajets $id_trajet): static
    {
        $this->id_trajet = $id_trajet;

        return $this;
    }

    public function getIdPassager(): ?Utilisateurs
    {
        return $this->id_passager;
    }

    public function setIdPassager(Utilisateurs $id_passager): static
    {
        $this->id_passager = $id_passager;

        return $this;
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
}
