<?php

namespace App\Controller;

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
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupération des statistiques pour le dashboard
        $userCount = $entityManager->getRepository(User::class)->count([]);
        $boatCount = $entityManager->getRepository(Boat::class)->count([]);
        $rentalCount = $entityManager->getRepository(Rental::class)->count([]);

        // On pourrait aussi récupérer les dernières locations, etc.
        $lastRentals = $entityManager->getRepository(Rental::class)->findBy(
            [],
            ['id' => 'DESC'],
            5
        );

        return $this->render('admin/dashboard.html.twig', [
            'userCount' => $userCount,
            'boatCount' => $boatCount,
            'rentalCount' => $rentalCount,
            'lastRentals' => $lastRentals,
        ]);
    }
}
