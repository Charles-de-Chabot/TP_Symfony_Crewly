<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RentalController extends AbstractController
{
    #[Route('/rental', name: 'app_rental')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('rental/index.html.twig', [
            'controller_name' => 'RentalController',
        ]);
    }
}
