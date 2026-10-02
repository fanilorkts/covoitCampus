<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/mon-profil/modifier', name: 'app_profil_modifier', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function modifier(Request $request, EntityManagerInterface $entityManager): Response
    {
    $utilisateur = $this->getUser();
    $form = $this->createForm(ProfilType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Profil mis à jour.');
        return $this->redirectToRoute('app_profil', ['id' => $utilisateur->getId()]);
    }

    return $this->render('profil/modifier.html.twig', ['form' => $form]);
}
}
