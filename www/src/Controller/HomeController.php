<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HomeController - Page d'accueil
 *
 * Rôle simple : Point d'entrée du site
 * Affiche les éléments mis en avant (ex: derniers bateaux ajoutés)
 */
class HomeController extends AbstractController
{
    /**
     * Page d'accueil
     * Récupère les 3 derniers bateaux pour la section "Nouveautés"
     */
    #[Route('/', name: 'app_home')]
    public function index(BoatRepository $boatRepository): Response
    {
        // Récupère les 3 derniers bateaux ajoutés pour la section "Nouveautés"
        $featuredBoats = $boatRepository->findBy(['isActive' => true], ['id' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'featuredBoats' => $featuredBoats,
        ]);
    }
}
