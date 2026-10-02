<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Trajets;
use App\Entity\Utilisateurs;
use App\Enum\Role;
use App\Repository\AvisRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $nbTermines = (int) $em->createQuery('SELECT COUNT(t.id) FROM App\Entity\Trajets t WHERE t.statut = :s')
            ->setParameter('s', Trajets::STATUTS_TERMINE)
            ->getSingleScalarResult();

        $taux = (float) $em->createQuery(
            'SELECT AVG((t.places_totales - t.places_restantes) / t.places_totales) FROM App\Entity\Trajets t WHERE t.statut = :s'
        )->setParameter('s', Trajets::STATUTS_TERMINE)->getSingleScalarResult();

        return $this->render('admin/index.html.twig', [
            'nbTermines' => $nbTermines,
            'taux' => $taux * 100,
            'nbUtilisateurs' => $em->getRepository(Utilisateurs::class)->count([]),
            'nbAvis' => $em->getRepository(Avis::class)->count([]),
        ]);
    }

    #[Route('/avis', name: 'app_admin_avis', methods: ['GET'])]
    public function avis(AvisRepository $avisRepository): Response
    {
        return $this->render('admin/avis.html.twig', [
            'avis' => $avisRepository->findBy([], ['date' => 'DESC']),
        ]);
    }

    #[Route('/avis/{id}/supprimer', name: 'app_admin_avis_supprimer', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function supprimerAvis(Avis $avi, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('supprimer_avis'.$avi->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($avi);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }

        return $this->redirectToRoute('app_admin_avis');
    }

    #[Route('/utilisateurs', name: 'app_admin_utilisateurs', methods: ['GET'])]
    public function utilisateurs(UtilisateursRepository $repository): Response
    {
        return $this->render('admin/utilisateurs.html.twig', [
            'utilisateurs' => $repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/utilisateurs/{id}/role', name: 'app_admin_utilisateur_role', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function changerRole(Utilisateurs $utilisateur, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('role'.$utilisateur->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($utilisateur->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('app_admin_utilisateurs');
        }

        $utilisateur->setRole($utilisateur->getRole() === Role::Admin ? Role::User : Role::Admin);
        $em->flush();
        $this->addFlash('success', 'Rôle modifié.');

        return $this->redirectToRoute('app_admin_utilisateurs');
    }
}