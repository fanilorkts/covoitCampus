<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Dashboard passager (maquette Figma "Passenger Dashboard").
 * Mêmes hypothèses de noms de propriétés que pour DriverDashboardController.
 */
final class PassengerDashboardController extends AbstractController
{
    // Estimation simple : 2,3 kg de CO2 évités par trajet partagé (à remplacer par ta règle)
    private const CO2_PER_RIDE = 2.3;

    #[Route('/dashboard/passenger', name: 'app_dashboard_passenger')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $em->getRepository(Reservation::class);

        // Prochain trajet confirmé
        $next = $reservations->createQueryBuilder('r')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->andWhere('t.dateHeure >= :now')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.dateHeure', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        // Demandes en attente
        $pending = $reservations->createQueryBuilder('r')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'En attente')
            ->orderBy('t.dateHeure', 'ASC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        // Historique récent : trajets terminés auxquels j'ai participé
        $history = $reservations->createQueryBuilder('r')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->andWhere('t.statut = :termine')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->setParameter('termine', 'Terminé')
            ->orderBy('t.dateHeure', 'DESC')
            ->setMaxResults(2)
            ->getQuery()->getResult();

        // Trajets habituels : les 3 couples origine/destination les plus fréquents
        $routes = $em->createQueryBuilder()
            ->select('t.origine AS origine, t.destination AS destination, COUNT(r.id) AS n')
            ->from(Reservation::class, 'r')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->groupBy('t.origine, t.destination')
            ->orderBy('n', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getArrayResult();

        // Statistiques
        $ridesTaken = (int) $reservations->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->andWhere('t.statut = :termine')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->setParameter('termine', 'Terminé')
            ->getQuery()->getSingleScalarResult();

        // Somme des prix des trajets terminés (nécessite la colonne prix sur Trajet)
        $spent = (float) $reservations->createQueryBuilder('r')
            ->select('COALESCE(SUM(t.prix), 0)')
            ->join('r.trajet', 't')
            ->andWhere('r.passager = :u')
            ->andWhere('r.statut = :statut')
            ->andWhere('t.statut = :termine')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->setParameter('termine', 'Terminé')
            ->getQuery()->getSingleScalarResult();

        $rating = $em->getRepository(Avis::class)->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->andWhere('a.cible = :u')
            ->setParameter('u', $user)
            ->getQuery()->getSingleScalarResult();

        return $this->render('dashboard/passenger.html.twig', [
            'user' => $user,
            'next' => $next,
            'pending' => $pending,
            'history' => $history,
            'routes' => $routes,
            'co2PerRide' => self::CO2_PER_RIDE,
            'stats' => [
                'rides' => $ridesTaken,
                'spent' => $spent,
                'co2' => round($ridesTaken * self::CO2_PER_RIDE, 1),
                'rating' => $rating !== null ? round((float) $rating, 1) : null,
            ],
        ]);
    }
}