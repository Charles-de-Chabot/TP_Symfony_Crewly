<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use App\Repository\ModelRepository;
use App\Repository\AdressRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/boat')]
final class BoatController extends AbstractController
{
    #[Route(name: 'app_boat_index', methods: ['GET'])]
    public function index(BoatRepository $boatRepository, TypeRepository $typeRepository, ModelRepository $modelRepository, AdressRepository $adressRepository, Request $request): Response
    {
        // ========== EXTRACTION DES PARAMÈTRES D'URL ==========
        $typeId = $request->query->getInt('type', 0);
        $modelId = $request->query->getInt('model', 0);
        $city = $request->query->get('city');

        // Si la ville est '0' (Toutes les villes) ou vide, on passe null au repository pour ne pas filtrer
        $cityFilter = ($city === '0' || $city === '') ? null : $city;
        $boats = $boatRepository->findAllWithFilters($typeId, $modelId, $cityFilter);

        // Récupération des villes distinctes pour le filtre
        $cities = $adressRepository->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        return $this->render('boat/index.html.twig', [
            'boats' => $boats,
            'types' => $typeRepository->findAll(),
            'models' => $modelRepository->findAll(),
            'cities' => $cities,
            // On renvoie les filtres actuels pour les garder sélectionnés dans le formulaire
            'currentType' => $typeId,
            'currentModel' => $modelId,
            'currentCity' => $city ?? '0',
        ]);
    }
}
