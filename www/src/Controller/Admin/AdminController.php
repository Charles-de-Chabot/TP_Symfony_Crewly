<?php

namespace App\Controller\Admin;

use App\Entity\Boat;
use App\Entity\Rental;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupération des compteurs pour le tableau de bord
        $userCount = $entityManager->getRepository(User::class)->count([]);
        $boatCount = $entityManager->getRepository(Boat::class)->count([]);
        $rentalCount = $entityManager->getRepository(Rental::class)->count([]);

        return $this->render('Admin/dashboard.html.twig', [
            'userCount' => $userCount,
            'boatCount' => $boatCount,
            'rentalCount' => $rentalCount,
        ]);
    }
}
