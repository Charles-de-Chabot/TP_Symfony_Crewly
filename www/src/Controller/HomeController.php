<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BoatRepository $boatRepository): Response
    {
        // Récupère les 3 derniers bateaux ajoutés pour la section "Nouveautés"
        $featuredBoats = $boatRepository->findBy([], ['id' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'featuredBoats' => $featuredBoats,
        ]);
    }
}
