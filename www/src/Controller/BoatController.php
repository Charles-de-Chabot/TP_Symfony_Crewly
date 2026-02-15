<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Boat;
use App\Repository\BoatRepository;
use App\Repository\ModelRepository;
use App\Repository\AdressRepository;
use App\Repository\FormulaRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * BoatController - Catalogue des bateaux
 *
 * CONCEPTS CLÉS :
 * - Filtres multiples : Type, Modèle, Ville, Disponibilité (Dates)
 * - QueryBuilder : Construction de requêtes SQL dynamiques via Doctrine
 * - Injection de dépendances : Multiples repositories nécessaires pour les filtres
 */
#[Route('/boat')]
final class BoatController extends AbstractController
{
    /**
     * Liste des bateaux avec système de filtrage avancé
     *
     * LOGIQUE DE FILTRE :
     * 1. Filtres statiques (Type, Modèle, Ville) via findAllWithFilters
     * 2. Filtre de disponibilité (Dates) via findAvailableBoats (sous-requête d'exclusion)
     * 3. Intersection des résultats
     */
    #[Route(name: 'app_boat_index', methods: ['GET'])]
    public function index(
        BoatRepository $boatRepository,
        TypeRepository $typeRepository,
        ModelRepository $modelRepository,
        AdressRepository $adressRepository,
        Request $request
    ): Response {
        // 1. Extraction des paramètres
        $typeId = $request->query->getInt('type', 0);
        $modelId = $request->query->getInt('model', 0);
        $city = $request->query->get('city');

        $cityFilter = ($city === '0' || $city === '') ? null : $city;

        /// 2. Logique pour filtrer la liste des MODÈLES
        if ($typeId > 0) {
            // On cherche les modèles qui sont liés à au moins un bateau de ce type
            $modelsForSelect = $modelRepository->createQueryBuilder('m')
                ->innerJoin('App\Entity\Boat', 'b', 'WITH', 'b.model = m')
                ->where('b.type = :typeId')
                ->setParameter('typeId', $typeId)
                ->distinct()
                ->orderBy('m.label', 'ASC')
                ->getQuery()
                ->getResult();

            // Sécurité : reset du model si incohérent (optionnel mais conseillé)
            // On vérifie si le modelId actuel est dans la liste des modèles possibles pour ce type
            $modelIds = array_map(fn($m) => $m->getId(), $modelsForSelect);
            if ($modelId > 0 && !in_array($modelId, $modelIds)) {
                $modelId = 0;
            }
        } else {
            $modelsForSelect = $modelRepository->findBy([], ['label' => 'ASC']);
        }

        // 3. Récupération des bateaux filtrés
        $boats = $boatRepository->findAllWithFilters($typeId, $modelId, $cityFilter);

        // 4. Récupération des villes
        $cities = $adressRepository->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        //Filtre les bateaux disponible par date
        $startStr = $request->query->get('start');
        $endStr = $request->query->get('end');

        if ($startStr && $endStr) {
            $start = new \DateTime($startStr);
            $end = new \DateTime($endStr);
            $availableBoats = $boatRepository->findAvailableBoats($start, $end);

            // On croise les résultats : on ne garde que les bateaux filtrés qui sont aussi disponibles
            $availableIds = array_map(fn($b) => $b->getId(), $availableBoats);
            $boats = array_filter($boats, fn($b) => in_array($b->getId(), $availableIds));
        }

        return $this->render('boat/index.html.twig', [
            'boats' => $boats,
            'types' => $typeRepository->findAll(),
            'models' => $modelsForSelect, // On utilise notre liste filtrée ici
            'cities' => $cities,
            'currentType' => $typeId,
            'currentModel' => $modelId,
            'currentCity' => $city ?? '0'
        ]);
    }

    /**
     * Fiche détail d'un bateau
     * Affiche les infos, l'adresse et les formules disponibles
     */
    #[Route('/{id}', name: 'app_boat_show', methods: ['GET'])]
    public function show(int $id, BoatRepository $boatRepository, AdressRepository $adressRepository, FormulaRepository $formulaRepository): Response
    {
        // ========== RÉCUPÉRATION DU boat ==========

        // Récupération du bateau (uniquement si actif)
        $boat = $boatRepository->findOneBy(['id' => $id, 'isActive' => true]);
        $adress = $boat ? $adressRepository->findOneBy(['id' => $boat->getAdress()]) : null;


        // Vérifier que le boat existe
        if (!$boat) {
            $this->addFlash('error', "Ce défi n'existe pas");
            return $this->redirectToRoute('app_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        //Verifier que l'adresss existe
        if (!$adress) {
            $this->addFlash('error', "Ce défi n'existe pas");
            return $this->redirectToRoute('app_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        // Récupération des formules
        $formulas = $formulaRepository->findAll();

        // ========== RENDU ==========

        return $this->render('boat/show.html.twig', [
            'boat' => $boat,
            'adress' => $adress,
            'formulas' => $formulas
        ]);
    }
}
