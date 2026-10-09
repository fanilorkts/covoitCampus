<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public const PAR_PAGE = 25;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    /** @return Vehicule[] */
    public function findDuProprietaire(Utilisateurs $proprietaire): array
    {
        return $this->findBy(['proprietaire' => $proprietaire], ['marque' => 'ASC', 'modele' => 'ASC']);
    }

    /**
     * Liste paginée (et filtrable) de tous les véhicules, pensée pour une table volumineuse :
     * seule la page demandée est chargée, le propriétaire est joint pour éviter le N+1.
     *
     * @return Paginator<Vehicule>
     */
    public function rechercher(?string $terme, int $page = 1, int $parPage = self::PAR_PAGE): Paginator
    {
        $qb = $this->createQueryBuilder('v')
            ->addSelect('p')
            ->join('v.proprietaire', 'p')
            ->orderBy('v.marque', 'ASC')
            ->addOrderBy('v.modele', 'ASC')
            ->addOrderBy('v.id', 'ASC')
            ->setFirstResult(max(0, $page - 1) * $parPage)
            ->setMaxResults($parPage);

        if (null !== $terme && '' !== trim($terme)) {
            $qb->andWhere('v.marque LIKE :q OR v.modele LIKE :q OR v.immatriculation LIKE :q OR v.couleur LIKE :q')
                ->setParameter('q', '%'.trim($terme).'%');
        }

        // Jointure to-one uniquement : pas besoin du mode "fetch join collection" (une requête de moins).
        return new Paginator($qb, fetchJoinCollection: false);
    }
}
