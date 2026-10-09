<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use App\Entity\Trajets;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Dashboard conducteur (maquette Figma "Driver Dashboard").
 *
 * Hypothèses sur tes entités (à adapter si tes noms diffèrent) :
 *  - Trajets     : id_conducteur, date_heure, statut, prix, origine, destination, places_restantes
 *  - Reservation : id_trajet, id_passager, statut, date_reservation
 *  - Avis        : id_trajet, id_auteur, id_cible, note, commentaire, date
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
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('t.statut IN (:statuts)')
            ->andWhere('t.date_heure >= :now')
            ->setParameter('u', $user)
            ->setParameter('statuts', ['Ouvert', 'Complet'])
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.date_heure', 'ASC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        // Trajets terminés, les 2 derniers
        $past = $em->getRepository(Trajets::class)->createQueryBuilder('t')
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('t.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Terminé')
            ->orderBy('t.date_heure', 'DESC')
            ->setMaxResults(2)
            ->getQuery()->getResult();

        // Demandes de réservation en attente sur mes trajets
        $requests = $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->join('r.id_trajet', 't')
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'En attente')
            ->orderBy('r.date_reservation', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        // Derniers avis reçus
        $reviews = $em->getRepository(Avis::class)->createQueryBuilder('a')
            ->andWhere('a.id_cible = :u')
            ->setParameter('u', $user)
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(2)
            ->getQuery()->getResult();

        // Statistiques
        $ridesShared = (int) $em->getRepository(Trajets::class)->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('t.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Terminé')
            ->getQuery()->getSingleScalarResult();

        $passengers = (int) $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.id_trajet', 't')
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->getQuery()->getSingleScalarResult();

        // Somme des prix des places confirmées (nécessite la colonne prix sur Trajet)
        $money = (float) $em->getRepository(Reservation::class)->createQueryBuilder('r')
            ->select('COALESCE(SUM(t.prix), 0)')
            ->join('r.id_trajet', 't')
            ->andWhere('t.id_conducteur = :u')
            ->andWhere('r.statut = :statut')
            ->setParameter('u', $user)
            ->setParameter('statut', 'Confirmée')
            ->getQuery()->getSingleScalarResult();

        $rating = $em->getRepository(Avis::class)->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->andWhere('a.id_cible = :u')
            ->setParameter('u', $user)
            ->getQuery()->getSingleScalarResult();

        $vehicules = $em->getRepository(Vehicule::class)->findBy(['proprietaire' => $user], ['marque' => 'ASC', 'modele' => 'ASC']);

        return $this->render('driverdashboard.html.twig', [
            'user' => $user,
            'vehicules' => $vehicules,
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