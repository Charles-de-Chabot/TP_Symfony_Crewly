<?php

namespace App\Controller\Admin;

use App\Entity\Boat;
use App\Form\BoatType;
use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/boat')]
#[IsGranted('ROLE_ADMIN')]
class BoatController extends AbstractController
{
    #[Route('/', name: 'app_admin_boat_index', methods: ['GET'])]
    public function index(BoatRepository $boatRepository): Response
    {
        return $this->render('Admin/boat/index.html.twig', [
            'boats' => $boatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_boat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boat = new Boat();
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boat);
            $entityManager->flush();

            $this->addFlash('success', 'Le bateau a été ajouté avec succès.');

            return $this->redirectToRoute('app_admin_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/boat/new.html.twig', [
            'boat' => $boat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_boat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boat $boat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le bateau a été modifié avec succès.');

            return $this->redirectToRoute('app_admin_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/boat/edit.html.twig', [
            'boat' => $boat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_boat_delete', methods: ['POST'])]
    public function delete(Request $request, Boat $boat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $boat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($boat);
            $entityManager->flush();
            $this->addFlash('success', 'Le bateau a été supprimé.');
        }
        return $this->redirectToRoute('app_admin_boat_index', [], Response::HTTP_SEE_OTHER);
    }
}
