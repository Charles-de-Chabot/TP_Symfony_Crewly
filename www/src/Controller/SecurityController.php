<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController - Gère la connexion/déconnexion
 * 
 * CONCEPTS CLÉS :
 * - AuthenticationUtils : récupère les erreurs de login et le dernier username
 * - login() : méthode VIDE (traitée par Symfony en arrière-plan)
 * - logout() : méthode VIDE (traitée par le firewall dans security.yaml)
 * - Le contrôleur affiche juste le formulaire de connexion
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion
     * 
     * @Route('/login', name: 'app_login')
     * 
     * FLUX :
     * 1. GET /login : affiche le formulaire de connexion
     * 2. POST /login : Symfony gère l'authentification automatiquement
     *    - Si correct : redirige vers la page demandée ou homepage
     *    - Si erreur : redisplay le formulaire avec le message d'erreur
     * 
     * @param AuthenticationUtils Service pour récupérer les infos d'authentification
     * 
     * @return Response Vue du formulaire de connexion
     * 
     * PÉDAGOGIE - DÉLÉGATION À SYMFONY :
     * - Le contrôleur ne valide PAS les credentials
     * - Le formulaire POST ne revient PAS ici
     * - Symfony gère tout via le firewall (sécurité.yaml)
     * - C'est de la "Convention over Configuration" de Symfony
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // ========== RÉCUPÉRATION DES DONNÉES DEPUIS LA DERNIÈRE TENTATIVE ==========

        /**
         * getLastAuthenticationError() :
         * - Récupère l'erreur de login (si la dernière tentative a échoué)
         * - Null si pas d'erreur
         * - Messages possibles : "Invalid credentials", "User not found", etc.
         */
        $error = $authenticationUtils->getLastAuthenticationError();

        /**
         * getLastUsername() :
         * - Récupère le dernier username saisi (pour le préremplir)
         * - Améliore UX (l'utilisateur ne doit pas le ressaisir après erreur)
         */
        $lastUsername = $authenticationUtils->getLastUsername();

        // ========== AFFICHAGE DU FORMULAIRE ==========

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,  // Préremplir le champ email/pseudo
            'error' => $error,                 // Afficher le message d'erreur
        ]);
    }

    /**
     * Déconnecte l'utilisateur
     * 
     * @Route('/logout', name: 'app_logout')
     * 
     * IMPORTANT : Cette méthode est VIDE par design
     * 
     * Pourquoi ?
     * - Symfony INTERCEPTE les requêtes vers /logout
     * - Le firewall (security.yaml) les traite avant qu'elles n'arrivent ici
     * - La déconnexion : destruction de la session, cookies supprimés, etc.
     * - Cette méthode ne s'exécute JAMAIS
     * 
     * Le LogicException est levé si elle était atteinte (ce qui ne devrait jamais arriver)
     * C'est une sécurité : si on essaie d'accéder à /logout directement, erreur
     * 
     * @throws \LogicException (jamais levée en production, juste une sécurité)
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode est interceptée par le firewall de Symfony
        // Elle ne s'exécutera jamais en conditions normales
        // Si vous la voyez lever une exception, il y a un problème de configuration
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}