<?php

namespace App\Entity;

use App\Repository\TrajetsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetsRepository::class)]
class Trajets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $origine = null;

    #[ORM\Column(length: 255)]
    private ?string $destination = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $date_heure = null;

    #[ORM\Column]
    private ?int $places_totales = null;

    #[ORM\Column(length: 255)]
    private ?string $places_restantes = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $id_conducteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDateHeure(): ?DateTime
    {
        return $this->date_heure;
    }

    public function setDateHeure(\DateTime $date_heure): static
    {
        $this->date_heure = $date_heure;

        return $this;
    }

    public function getPlacesTotales(): ?int
    {
        return $this->places_totales;
    }

    public function setPlacesTotales(int $places_totales): static
    {
        $this->places_totales = $places_totales;

        return $this;
    }

    public function getPlacesRestantes(): ?int
    {
        return $this->places_restantes;
    }

    public function setPlacesRestantes(int $places_restantes): static
    {
        $this->places_restantes = $places_restantes;

        return $this;
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

    public function getIdConducteur(): ?string
    {
        return $this->id_conducteur;
    }

    public function setIdConducteur(string $id_conducteur): static
    {
        $this->id_conducteur = $id_conducteur;

        return $this;
    }
}
