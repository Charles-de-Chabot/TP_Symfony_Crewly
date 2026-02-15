<?php

namespace App\Controller\Admin;

use App\Entity\Rental;
use App\Repository\RentalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/rental')]
#[IsGranted('ROLE_ADMIN')]
final class RentalController extends AbstractController
{
    #[Route('/', name: 'app_admin_rental_index', methods: ['GET'])]
    public function index(RentalRepository $rentalRepository): Response
    {
        return $this->render('Admin/Rental/index.html.twig', [
            'rentals' => $rentalRepository->findBy([], ['rentalStart' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_rental_show', methods: ['GET'])]
    public function show(Rental $rental): Response
    {
        return $this->render('Admin/Rental/show.html.twig', [
            'rental' => $rental,
        ]);
    }
}
