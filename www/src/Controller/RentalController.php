<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use App\Repository\RentalRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RentalController extends AbstractController
{
    #[Route('/rental', name: 'app_rental')]
    #[IsGranted('ROLE_USER')]
    public function index(RentalRepository $rentalRepository, UserRepository $userRepository, BoatRepository $boatRepository,): Response
    {



        return $this->render('rental/index.html.twig', [
            'controller_name' => 'RentalController',
        ]);
    }
}
