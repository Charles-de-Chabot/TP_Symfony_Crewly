<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\RentalRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Admin/User/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user, RentalRepository $rentalRepository): Response
    {
        return $this->render('Admin/User/show.html.twig', [
            'user' => $user,
            'rentals' => $rentalRepository->findBy(['user' => $user], ['rentalStart' => 'DESC']),
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_user_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Empêcher l'admin de se désactiver lui-même
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        if ($this->isCsrfTokenValid('toggle-status' . $user->getId(), $request->request->get('_token'))) {
            $user->setIsActive(!$user->isActive());
            $entityManager->flush();
            $this->addFlash('success', 'Le statut de l\'utilisateur a été modifié.');
        }

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_admin_user_index'));
    }
}
