<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Utilisateurs;
use App\Entity\Trajets;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function countPassager(Utilisateurs $passager): int
    {
    return (int) $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->join('r.id_trajet', 't')
        ->where('r.id_passager = :p')
        ->andWhere('r.statut = :confirme')
        ->andWhere('t.statut = :termine')
        ->setParameter('p', $passager)
        ->setParameter('confirme', Reservation::STATUS_CONFIRME)
        ->setParameter('termine', Trajets::STATUTS_TERMINE)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function passagerLePlusFrequent(Utilisateurs $conducteur): ?array
{
    return $this->createQueryBuilder('r')
        ->select('IDENTITY(r.id_passager) AS passagerId, COUNT(r.id) AS nb')
        ->join('r.id_trajet', 't')
        ->where('t.id_conducteur = :c')
        ->andWhere('t.statut = :termine')
        ->andWhere('r.statut = :confirme')
        ->groupBy('r.id_passager')
        ->orderBy('nb', 'DESC')
        ->setMaxResults(1)
        ->setParameter('c', $conducteur)
        ->setParameter('termine', Trajets::STATUTS_TERMINE)
        ->setParameter('confirme', Reservation::STATUS_CONFIRME)
        ->getQuery()
        ->getOneOrNullResult();
}

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
