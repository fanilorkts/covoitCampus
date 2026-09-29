<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_reservation = null;

    #[ORM\Column]
    private ?int $id_trajet = null;

    #[ORM\Column]
    private ?int $id_passager = null;

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

    public function getIdTrajet(): ?int
    {
        return $this->id_trajet;
    }

    public function setIdTrajet(int $id_trajet): static
    {
        $this->id_trajet = $id_trajet;

        return $this;
    }

    public function getIdPassager(): ?int
    {
        return $this->id_passager;
    }

    public function setIdPassager(int $id_passager): static
    {
        $this->id_passager = $id_passager;

        return $this;
    }
}
