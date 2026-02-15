<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Entity\Rental;
use App\Entity\User;
use App\Repository\AdressRepository;
use App\Repository\FormulaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rental')]
final class RentalController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_rental_new', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // S'assure que l'utilisateur est connecté
    public function new(
        Request $request,
        Boat $boat,
        FormulaRepository $formulaRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // 1. Récupérer l'utilisateur en session
        /** @var User $user */
        $user = $this->getUser();

        // 2. Récupérer les données du formulaire
        // Les dates saisies par l'utilisateur
        $startStr = $request->request->get('start');
        $endStr = $request->request->get('end');

        // Les formules sélectionnées (tableau d'IDs)
        $formulaIds = $request->request->all('formulas');

        // Validation basique des dates
        if (!$startStr || !$endStr) {
            $this->addFlash('error', 'Veuillez sélectionner des dates de location.');
            return $this->redirectToRoute('app_boat_show', ['id' => $boat->getId()]);
        }

        // 3. Création et hydratation de l'entité Rental
        $rental = new Rental();
        $rental->setUser($user);
        $rental->setBoat($boat);

        try {
            $start = new \DateTime($startStr);
            $end = new \DateTime($endStr);
            $rental->setRentalStart($start);
            $rental->setRentalEnd($end);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Format de date invalide.');
            return $this->redirectToRoute('app_boat_show', ['id' => $boat->getId()]);
        }

        // Calcul de la différence en jours
        $diffDays = $start->diff($end)->days;
        // Si c'est le même jour, on compte 1 jour. Sinon, la date de fin ne compte pas (ex: du 1er au 2 = 1 jour)
        $days = ($diffDays === 0) ? 1 : $diffDays;
        $weeks = floor($days / 7);
        $restDays = $days % 7;

        // Logique "Mixte" du JS : plus de 7 jours et pas un multiple exact de 7
        $isMixedMode = ($days > 7 && $restDays !== 0);

        $calculatedPrice = 0;

        // 4. Application de la logique de tarification (transcription du JS)
        if ($isMixedMode) {
            // Mode Mixte : On combine Semaine (4) et Journée (3)
            // Prix = (Nombre de semaines * Prix Semaine) + (Jours restants * Prix Journée)
            $f3 = $formulaRepository->find(3); // Journée
            $f4 = $formulaRepository->find(4); // Semaine

            if ($f3 && $f4) {
                $calculatedPrice = ($weeks * $f4->getPrice()) + ($restDays * $f3->getPrice());
                $rental->addFormula($f3);
                $rental->addFormula($f4);
            }
        } elseif ($days == 1) {
            // Même jour : On respecte le choix de l'utilisateur (1, 2 ou 3)
            foreach ($formulaIds as $formulaId) {
                if ($formula = $formulaRepository->find($formulaId)) {
                    $calculatedPrice += $formula->getPrice();
                    $rental->addFormula($formula);
                }
            }
        } else {
            // Plus d'un jour, mais pas en mode mixte
            if ($restDays == 0) {
                // Semaines complètes (7, 14, 21 jours...) -> Formule 4 uniquement
                if ($f4 = $formulaRepository->find(4)) {
                    $calculatedPrice = $weeks * $f4->getPrice();
                    $rental->addFormula($f4);
                }
            } else {
                // Moins d'une semaine (2 à 6 jours) -> Formule 3 uniquement
                if ($f3 = $formulaRepository->find(3)) {
                    $calculatedPrice = $days * $f3->getPrice();
                    $rental->addFormula($f3);
                }
            }
        }

        // Calcul du prix total sécurisé
        $rental->setRentalPrice((float) $calculatedPrice);

        // 5. Enregistrement en base de données
        $entityManager->persist($rental);
        $entityManager->flush();

        // Signal pour nettoyer le localStorage côté client
        $this->addFlash('clear_storage', 'true');

        // Si l'utilisateur n'a pas d'adresse, on le redirige vers l'édition du profil
        if (!$user->getAdress()) {
            $this->addFlash('success', 'Votre location est bien enregistrée. Pour la finaliser, veuillez remplir vos informations complémentaires.');
            return $this->redirectToRoute('app_profile_edit');
        }

        $this->addFlash('success', 'Votre location a été enregistrée avec succès !');
        return $this->redirectToRoute('app_profile_show');
    }

    #[Route('/{id}/edit', name: 'app_rental_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Request $request,
        Rental $rental,
        FormulaRepository $formulaRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette location.');
        }

        if ($request->isMethod('POST')) {
            $startStr = $request->request->get('start');
            $endStr = $request->request->get('end');

            if (!$startStr || !$endStr) {
                $this->addFlash('error', 'Veuillez sélectionner des dates.');
                return $this->redirectToRoute('app_rental_edit', ['id' => $rental->getId()]);
            }

            try {
                $start = new \DateTime($startStr);
                $end = new \DateTime($endStr);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Format de date invalide.');
                return $this->redirectToRoute('app_rental_edit', ['id' => $rental->getId()]);
            }

            // Mise à jour des dates
            $rental->setRentalStart($start);
            $rental->setRentalEnd($end);

            // Recalcul du prix (Logique identique à new)
            $diffDays = $start->diff($end)->days;
            $days = ($diffDays === 0) ? 1 : $diffDays;
            $weeks = floor($days / 7);
            $restDays = $days % 7;
            $isMixedMode = ($days > 7 && $restDays !== 0);

            $calculatedPrice = 0;

            // On vide les anciennes formules pour recalculer
            $rental->getFormulas()->clear();

            if ($isMixedMode) {
                $f3 = $formulaRepository->find(3); // Journée
                $f4 = $formulaRepository->find(4); // Semaine
                if ($f3 && $f4) {
                    $calculatedPrice = ($weeks * $f4->getPrice()) + ($restDays * $f3->getPrice());
                    $rental->addFormula($f3);
                    $rental->addFormula($f4);
                }
            } elseif ($days == 1) {
                // Pour l'édition simple, on force la formule Journée (3) par défaut si 1 jour
                $f3 = $formulaRepository->find(3);
                if ($f3) {
                    $calculatedPrice = $f3->getPrice();
                    $rental->addFormula($f3);
                }
            } else {
                if ($restDays == 0 && $f4 = $formulaRepository->find(4)) {
                    $calculatedPrice = $weeks * $f4->getPrice();
                    $rental->addFormula($f4);
                } elseif ($f3 = $formulaRepository->find(3)) {
                    $calculatedPrice = $days * $f3->getPrice();
                    $rental->addFormula($f3);
                }
            }

            $rental->setRentalPrice((float) $calculatedPrice);
            $rental->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Votre location a été modifiée avec succès.');
            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('rental/edit.html.twig', [
            'rental' => $rental,
        ]);
    }

    #[Route('/{id}', name: 'app_rental_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Rental $rental, AdressRepository $adressRepository): Response
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette location.');
        }

        $boat = $rental->getBoat();
        $adress = $boat ? $adressRepository->findOneBy(['id' => $boat->getAdress()]) : null;

        return $this->render('rental/show.html.twig', [
            'rental' => $rental,
            'boat' => $boat,
            'adress' => $adress,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_rental_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        if ($rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette location.');
        }

        if ($rental->getRentalStart() <= new \DateTime()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler une location passée ou en cours.');
            return $this->redirectToRoute('app_profile_show');
        }

        if ($this->isCsrfTokenValid('delete' . $rental->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rental);
            $entityManager->flush();
            $this->addFlash('success', 'La location a été annulée.');
        }

        return $this->redirectToRoute('app_profile_show');
    }
}
