<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use App\Entity\Trajets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Dashboard conducteur (maquette Figma "Driver Dashboard").
 *
 * Hypothèses sur tes entités (à adapter si tes noms diffèrent) :
 *  - Trajet      : conducteur, dateHeure, statut, prix, origine, destination, placesRestantes
 *  - Reservation : trajet, passager, statut ('Confirmée', 'En attente', 'Refusée')
 *  - Avis        : cible, auteur, note, commentaire, date
 */
final class DriverDashboardController extends AbstractController
{
    #[Route('/dashboard/driver', name: 'app_dashboard_driver')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Test sans login (en attendant la partie de ton collègue) :
        // décommente la ligne suivante et importe App\Entity\Utilisateur
        // $user = $em->find(\App\Entity\Utilisateur::class, 2);

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Trajets à venir (Ouvert ou Complet), les 3 prochains
        $upcoming = $em->getRepository(Trajets::class)->createQueryBuilder('t')
            ->andWhere('t.conducteur = :u')
            ->andWhere('t.statut IN (:statuts)')
            ->andWhere('t.dateHeure >= :now')
            ->setParameter('u', $user)
            ->setParameter('statuts', ['Ouvert', 'Complet'])
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.dateHeure', 'ASC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        // Trajets terminés, les 2 derniers
        $past = $em->getRepository(Trajets::class)->createQueryBuilder('t')
            ->andWhere('t.conducteur = :u')
            ->andWhere('t.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Terminé')
            ->orderBy('t.dateHeure', 'DESC')
            ->setMaxResults(2)
            ->getQuery()->getResult();

        // Demandes de réservation en attente sur mes trajets
        $requests = $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->join('r.trajet', 't')
            ->andWhere('t.conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'En attente')
            ->orderBy('r.dateReservation', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        // Derniers avis reçus
        $reviews = $em->getRepository(Avis::class)->createQueryBuilder('a')
            ->andWhere('a.cible = :u')
            ->setParameter('u', $user)
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(2)
            ->getQuery()->getResult();

        // Statistiques
        $ridesShared = (int) $em->getRepository(Trajets::class)->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.conducteur = :u')
            ->andWhere('t.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Terminé')
            ->getQuery()->getSingleScalarResult();

        $passengers = (int) $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.trajet', 't')
            ->andWhere('t.conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->getQuery()->getSingleScalarResult();

        // Somme des prix des places confirmées (nécessite la colonne prix sur Trajet)
        $money = (float) $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->select('COALESCE(SUM(t.prix), 0)')
            ->join('r.trajet', 't')
            ->andWhere('t.conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->getQuery()->getSingleScalarResult();

        $rating = $em->getRepository(Avis::class)->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->andWhere('a.cible = :u')
            ->setParameter('u', $user)
            ->getQuery()->getSingleScalarResult();

        return $this->render('dashboard/driver.html.twig', [
            'user' => $user,
            'upcoming' => $upcoming,
            'past' => $past,
            'requests' => $requests,
            'reviews' => $reviews,
            'stats' => [
                'rides' => $ridesShared,
                'money' => $money,
                // Estimation simple : 2,3 kg de CO2 évités par passager transporté (à remplacer par ta règle)
                'co2' => round($passengers * 2.3, 1),
                'rating' => $rating !== null ? round((float) $rating, 1) : null,
            ],
        ]);
    }
}