<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Entity\Rental;
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
        $user = $this->getUser();

        // 2. Récupérer les données du formulaire
        // Les dates saisies par l'utilisateur
        $startStr = $request->request->get('start');
        $endStr = $request->request->get('end');

        // Le prix total calculé par le JavaScript dans show.html.twig
        // (Note : Idéalement, il faudrait recalculer le prix ici pour éviter toute modification malveillante)
        $totalPrice = $request->request->get('totalPrice');

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
            $rental->setRentalStart(new \DateTime($startStr));
            $rental->setRentalEnd(new \DateTime($endStr));
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

        $this->addFlash('success', 'Votre location a été enregistrée avec succès !');

        return $this->redirectToRoute('app_boat_index');
    }
}
