<?php

namespace App\Repository;

use App\Entity\Trajets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trajets>
 */
class TrajetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trajets::class);
    }

    public function RechercheTrajets(?string $origine, ?string $destination, ?\DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', Trajets::STATUTS_OUVERT)
            ->orderBy('t.date_heure', 'ASC');
        

        if ($origine) {
            $qb->andWhere('t.origine LIKE :origine')
                ->setParameter('origine', '%' . $origine . '%');
        }
        if ($destination) {
            $qb->andWhere('t.destination LIKE :destination')
                ->setParameter('destination', '%' . $destination . '%');
        }

        if ($date) {
            $qb->andWhere('t.date_heure BETWEEN :debut AND :fin')
            ->setParameter('debut', \DateTime::createFromInterface($date)->setTime(0, 0, 0))
            ->setParameter('fin', \DateTime::createFromInterface($date)->setTime(23, 59, 59));
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Trajets[] Returns an array of Trajets objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trajets
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
