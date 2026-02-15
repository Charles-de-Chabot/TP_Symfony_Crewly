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

/**
 * Admin/UserController - Gestion des utilisateurs par l'administrateur
 *
 * CONCEPTS CLÉS :
 * - #[IsGranted('ROLE_ADMIN')] : Sécurise tout le contrôleur pour les admins uniquement
 * - EntityManagerInterface : Utilisé pour modifier le statut (flush)
 * - CSRF Token : Protection contre les attaques Cross-Site Request Forgery sur les actions sensibles
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    /**
     * Liste tous les utilisateurs inscrits
     *
     * @return Response Vue tableau de bord des utilisateurs
     */
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Admin/User/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Affiche le détail d'un utilisateur et son historique de locations
     *
     * @param User $user L'entité est injectée automatiquement via le ParamConverter (id dans l'URL)
     * @param RentalRepository Pour récupérer l'historique des locations de cet utilisateur
     */
    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user, RentalRepository $rentalRepository): Response
    {
        return $this->render('Admin/User/show.html.twig', [
            'user' => $user,
            'rentals' => $rentalRepository->findBy(['user' => $user], ['rentalStart' => 'DESC']),
        ]);
    }

    /**
     * Active ou désactive un compte utilisateur (Ban/Unban)
     *
     * @param Request Pour vérifier le token CSRF
     * @param User L'utilisateur à modifier
     * @param EntityManagerInterface Pour sauvegarder le changement (flush)
     *
     * LOGIQUE :
     * 1. Vérifie que l'admin ne se désactive pas lui-même
     * 2. Vérifie le token CSRF pour la sécurité
     * 3. Inverse le booléen isActive
     * 4. Redirige vers la page précédente (referer)
     */
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
