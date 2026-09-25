<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $id_trajet = null;

    #[ORM\Column(length: 255)]
    private ?string $id_auteur = null;

    #[ORM\Column(length: 255)]
    private ?string $id_cible = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getIdTrajet(): ?string
    {
        return $this->id_trajet;
    }

    public function setIdTrajet(string $id_trajet): static
    {
        $this->id_trajet = $id_trajet;

        return $this;
    }

    public function getIdAuteur(): ?string
    {
        return $this->id_auteur;
    }

    public function setIdAuteur(string $id_auteur): static
    {
        $this->id_auteur = $id_auteur;

        return $this;
    }


    public function getIdCible(): ?string
    {
        return $this->id_cible;
    }

    public function setIdCible(string $id_cible): static
    {
        $this->id_cible = $id_cible;

        return $this;
    }
}
