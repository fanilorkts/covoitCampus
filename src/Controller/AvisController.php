<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use App\Entity\Trajets;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetsRepository;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/avis')]
#[IsGranted('ROLE_USER')]
final class AvisController extends AbstractController
{
    #[Route('/trajet/{id}/noter/{cibleId}', name: 'app_avis_noter', methods: ['GET', 'POST'], requirements: ['id' => '\d+', 'cibleId' => '\d+'])]
    public function noter(
        int $id,
        int $cibleId,
        Request $request,
        TrajetsRepository $trajetsRepository,
        UtilisateursRepository $utilisateursRepository,
        ReservationRepository $reservationRepository,
        AvisRepository $avisRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $trajetsRepository->cloturerTrajetsPasses();
        $trajet = $trajetsRepository->find($id);
        $cible = $utilisateursRepository->find($cibleId);
        if (!$trajet || !$cible) {
            throw $this->createNotFoundException();
        }

        $auteur = $this->getUser();
        $conducteur = $trajet->getIdConducteur();

        if ($trajet->getStatut() !== Trajets::STATUTS_TERMINE) {
            $this->addFlash('error', 'Vous ne pouvez noter qu\'après la fin du trajet.');
            return $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
        }

        $estPassagerConfirme = fn ($u) => $reservationRepository->findOneBy([
            'id_trajet' => $trajet,
            'id_passager' => $u,
            'statut' => Reservation::STATUS_CONFIRME,
        ]) !== null;

        $auteurEstConducteur = $auteur->getId() === $conducteur->getId();
        $cibleEstConducteur = $cible->getId() === $conducteur->getId();

        $autorise = ($auteurEstConducteur && $estPassagerConfirme($cible))
            || ($cibleEstConducteur && $estPassagerConfirme($auteur));
        if (!$autorise) {
            throw $this->createAccessDeniedException();
        }

        if ($avisRepository->findOneBy(['id_trajet' => $trajet, 'id_auteur' => $auteur, 'id_cible' => $cible])) {
            $this->addFlash('error', 'Vous avez déjà noté cette personne pour ce trajet.');
            return $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
        }

        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setTrajet($trajet);
            $avi->setAuteur($auteur);
            $avi->setCible($cible);
            $avi->setDate(new \DateTime());
            $entityManager->persist($avi);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre avis !');
            return $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
        }

        return $this->render('avis/noter.html.twig', [
            'form' => $form,
            'trajet' => $trajet,
            'cible' => $cible,
        ]);
    }
}