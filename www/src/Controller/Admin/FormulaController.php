<?php

namespace App\Controller\Admin;

use App\Entity\Formula;
use App\Form\FormulaType;
use App\Repository\FormulaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Admin/FormulaController - Gestion des formules de location
 *
 * CONCEPTS CLÉS :
 * - CRUD simple : Gestion d'une entité de configuration (Tarifs)
 * - Impact Métier : Ces formules sont utilisées dynamiquement dans le calcul du prix (RentalController)
 * - CSRF : Protection indispensable sur la méthode de suppression
 */
#[Route('/admin/formula')]
#[IsGranted('ROLE_ADMIN')]
class FormulaController extends AbstractController
{
    /**
     * Liste toutes les formules de tarification disponibles
     */
    #[Route('/', name: 'app_admin_formula_index', methods: ['GET'])]
    public function index(FormulaRepository $formulaRepository): Response
    {
        return $this->render('Admin/Formula/index.html.twig', [
            'formulas' => $formulaRepository->findAll(),
        ]);
    }

    /**
     * Ajoute une nouvelle formule de prix
     */
    #[Route('/new', name: 'app_admin_formula_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formula = new Formula();
        $form = $this->createForm(FormulaType::class, $formula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formula);
            $entityManager->flush();

            $this->addFlash('success', 'La formule a été créée avec succès.');

            return $this->redirectToRoute('app_admin_formula_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Formula/new.html.twig', [
            'formula' => $formula,
            'form' => $form,
        ]);
    }

    /**
     * Modifie une formule existante
     */
    #[Route('/{id}/edit', name: 'app_admin_formula_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formula $formula, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaType::class, $formula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La formule a été modifiée avec succès.');

            return $this->redirectToRoute('app_admin_formula_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Formula/edit.html.twig', [
            'formula' => $formula,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une formule
     * Vérifie le token CSRF pour éviter les suppressions accidentelles via des liens malveillants
     */
    #[Route('/{id}', name: 'app_admin_formula_delete', methods: ['POST'])]
    public function delete(Request $request, Formula $formula, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formula->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formula);
            $entityManager->flush();
            $this->addFlash('success', 'La formule a été supprimée.');
        }
        return $this->redirectToRoute('app_admin_formula_index', [], Response::HTTP_SEE_OTHER);
    }
}
