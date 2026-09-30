<?php

namespace App\Entity;

use App\Repository\TrajetsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetsRepository::class)]
class Trajets
{

    public const STATUTS_OUVERT = 'Ouvert';
    public const STATUTS_COMPLET = 'Complet';
    public const STATUTS_TERMINE = 'Terminé';
    
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

    #[ORM\Column]
    private ?int $places_restantes = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne (targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: "id_conducteur", referencedColumnName: "id")
    ]
    private ?Utilisateurs $id_conducteur = null;

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

    public function getDateHeure(): ?\DateTime
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

    public function getIdConducteur(): ?Utilisateurs
    {
        return $this->id_conducteur;
    }

    public function setIdConducteur(Utilisateurs $id_conducteur): static
    {
        $this->id_conducteur = $id_conducteur;

        return $this;
    }

    public function reserverPlace (Utilisateurs $passager) : bool
    {

        if ($this->statut !== self::STATUTS_OUVERT) {
            throw new \Exception ("Désolé, ce trajet n'est pas ouvert à la réservation.");
        }

        if ($this->places_restantes <= 0){
            throw new \Exception ("Oh non ! Il n'y a plus de place disponible pour ce trajet");
        }

        if ($this->id_conducteur === $passager) {
            throw new \Exception ("Vous ne pouvez pas réserver une place pour votre propre trajet.");
        }

        $this->places_restantes--;

        

        if ($this->places_restantes === 0) {
            $this->statut = self::STATUTS_COMPLET;
        }


        return true;
    }

}
