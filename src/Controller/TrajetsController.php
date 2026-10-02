<?php

namespace App\Controller;

use App\Entity\Trajets;
use App\Form\TrajetsType;
use App\Repository\ReservationRepository;
use App\Repository\TrajetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\AvisRepository;
use App\Repository\UtilisateursRepository;

#[Route('/trajets')]
final class TrajetsController extends AbstractController
{
    #[Route(name: 'app_trajets_index', methods: ['GET'])]
    public function index(Request $request, TrajetsRepository $trajetsRepository): Response
    {
        $trajetsRepository->cloturerTrajets();
        $dateSaisie = $request->query->get('date_saisie');
        $date = $dateSaisie ? new \DateTime($dateSaisie) : null;

        return $this->render('trajets/index.html.twig', [
            'trajets' => $trajetsRepository->RechercheTrajets(
                $request->query->get('origine'),
                $request->query->get('destination'),
                $date
            ),
        ]);
    }

    #[Route('/new', name: 'app_trajets_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trajet = new Trajets();
        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setIdConducteur($this->getUser());
            $trajet->setPlacesRestantes($trajet->getPlacesTotales());
            $trajet->setStatut(Trajets::STATUTS_OUVERT);

            $entityManager->persist($trajet);
            $entityManager->flush();

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/new.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/mes-trajets', name: 'app_trajets_mes_trajets', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesTrajets(TrajetsRepository $trajetsRepository, ReservationRepository $reservationRepository, UtilisateursRepository $utilisateursRepository): Response
{
    $trajetsRepository->cloturerTrajetsPasses();
    $user = $this->getUser();
    $frequent = $reservationRepository->passagerLePlusFrequent($user);

    return $this->render('trajets/mes_trajets.html.twig', [
        'trajets' => $trajetsRepository->findBy(['id_conducteur' => $user], ['date_heure' => 'DESC']),
        'nbRealises' => $trajetsRepository->countTerminesCommeConducteur($user),
        'passagerFrequent' => $frequent ? $utilisateursRepository->find($frequent['passagerId']) : null,
        'nbFois' => $frequent['nb'] ?? 0,
    ]);
}

    #[Route('/{id}/terminer', name: 'app_trajets_terminer', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function terminer(Trajets $trajet, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($trajet->getIdConducteur()->getId() !== $this->getUser()->getId()
            || !$this->isCsrfTokenValid('terminer'.$trajet->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($trajet->getStatut() !== Trajets::STATUTS_TERMINE) {
            $trajet->setStatut(Trajets::STATUTS_TERMINE);
            $entityManager->flush();
            $this->addFlash('success', 'Trajet terminé. Vous pouvez maintenant noter vos passagers.');
        }

        return $this->redirectToRoute('app_trajets_show', ['id' => $trajet->getId()]);
    }

    #[Route('/{id}', name: 'app_trajets_show', methods: ['GET'], requirements: ['id' => '\d+'])]
   public function show(Trajets $trajet, ReservationRepository $reservationRepository, TrajetsRepository $trajetsRepository, AvisRepository $avisRepository): Response
    {
        $trajetsRepository->cloturerTrajetsPasses();
        $reservations = $reservationRepository->findBy(['id_trajet' => $trajet]);

        $notes = [];
        foreach ($reservations as $r) {
            $idPassager = $r->getIdPassager()->getId();
            $notes[$idPassager] = $avisRepository->noteMoyenne($idPassager);
        }

        return $this->render('trajets/show.html.twig', [
            'trajet' => $trajet,
            'reservations' => $reservations,
            'notes' => $notes,
            'noteConducteur' => $avisRepository->noteMoyenne($trajet->getIdConducteur()->getId()),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trajets_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Trajets $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($trajet->getIdConducteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TrajetsType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajets/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trajets_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trajets $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($trajet->getIdConducteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $trajet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trajet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trajets_index', [], Response::HTTP_SEE_OTHER);
    }
}