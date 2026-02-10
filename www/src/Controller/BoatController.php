<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use App\Repository\ModelRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/boat')]
final class BoatController extends AbstractController
{
    #[Route(name: 'app_boat_index', methods: ['GET'])]
    public function index(BoatRepository $boatRepository, TypeRepository $typeRepository, ModelRepository $modelRepository, Request $request): Response
    {
        // ========== EXTRACTION DES PARAMÈTRES D'URL ==========
        $typeId = $request->query->getInt('type', 0);
        $modelId = $request->query->getInt('model', 0);
        $city = (string) $request->query->get('city');

        $boats = $boatRepository->findAllWithFilters($typeId, $modelId, $city);

        return $this->render('boat/index.html.twig', [
            'boats' => $boats,
            'types' => $typeRepository->findAll(),
            'models' => $modelRepository->findAll(),
            // On renvoie les filtres actuels pour les garder sélectionnés dans le formulaire
            'currentType' => $typeId,
            'currentModel' => $modelId,
            'currentCity' => $city,
        ]);
    }
}
