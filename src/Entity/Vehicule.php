<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_VEHICULE_IMMATRICULATION', fields: ['immatriculation'])]
#[UniqueEntity(fields: ['immatriculation'], message: 'Un véhicule avec cette immatriculation est déjà enregistré.')]
class Vehicule
{
    public const ANNEE_MIN = 1950;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La marque est obligatoire.')]
    #[Assert\Length(max: 50)]
    private ?string $marque = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le modèle est obligatoire.')]
    #[Assert\Length(max: 50)]
    private ?string $modele = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(max: 30)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'L\'immatriculation est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^[A-Z0-9][A-Z0-9-]{2,13}[A-Z0-9]$/',
        message: 'Immatriculation invalide (lettres, chiffres et tirets uniquement, ex : AB-123-CD).'
    )]
    private ?string $immatriculation = null;

    /** Nombre de places total, conducteur inclus. */
    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'Le nombre de places est obligatoire.')]
    #[Assert\Range(min: 2, max: 9, notInRangeMessage: 'Le nombre de places doit être compris entre {{ min }} et {{ max }}.')]
    private ?int $nbPlaces = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(name: 'id_proprietaire', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateurs $proprietaire = null;

    #[Assert\Callback]
    public function validerAnnee(ExecutionContextInterface $context): void
    {
        $max = (int) date('Y') + 1;

        if (null !== $this->annee && ($this->annee < self::ANNEE_MIN || $this->annee > $max)) {
            $context->buildViolation('L\'année doit être comprise entre {{ min }} et {{ max }}.')
                ->setParameter('{{ min }}', (string) self::ANNEE_MIN)
                ->setParameter('{{ max }}', (string) $max)
                ->atPath('annee')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = trim($marque);

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = trim($modele);

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $couleur = null === $couleur ? null : trim($couleur);
        $this->couleur = '' === $couleur ? null : $couleur;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    /**
     * Normalise la plaque (majuscules, espaces → tirets) pour que
     * "ab 123 cd" et "AB-123-CD" soient considérées comme identiques.
     */
    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = preg_replace('/[\s-]+/', '-', strtoupper(trim($immatriculation)));

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(?int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getProprietaire(): ?Utilisateurs
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Utilisateurs $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function __toString(): string
    {
        return trim(sprintf('%s %s (%s)', $this->marque, $this->modele, $this->immatriculation));
    }
}
