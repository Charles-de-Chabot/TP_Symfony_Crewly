<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * RegistrationController - Gère l'inscription des nouveaux utilisateurs
 * 
 * CONCEPTS CLÉS :
 * - UserPasswordHasherInterface : hashe les mots de passe (ne JAMAIS stocker en clair !)
 * - Security::login() : connecte automatiquement après inscription (UX fluide)
 * - EntityManager : crée une nouvelle entité User en BD
 * - Injection de dépendances : tous les services sont injectés automatiquement par Symfony
 */
class RegistrationController extends AbstractController
{
    /**
     * Affiche et traite le formulaire d'inscription
     * 
     * @Route('/register', name: 'app_register')
     *   - Pas de méthode spécifiée = GET et POST acceptées
     * 
     * @param Request Contient les données POST du formulaire
     * @param UserPasswordHasherInterface Service pour hacher les mots de passe
     * @param Security Service pour authentifier l'utilisateur
     * @param EntityManagerInterface Service Doctrine pour persister
     * 
     * @return Response Vue du formulaire ou redirection après succès
     * 
     * PÉDAGOGIE - CYCLE D'INSCRIPTION :
     * 1. GET : afficher le formulaire vide
     * 2. POST : valider les données
     * 3. Hacher le mot de passe
     * 4. Créer l'entité User avec les données
     * 5. Persister et flush() en BD
     * 6. Connecter automatiquement (Security::login)
     * 7. Rediriger vers la page d'accueil
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        // ========== CRÉATION DE L'ENTITÉ USER ==========

        $user = new User();

        // ========== CRÉATION ET TRAITEMENT DU FORMULAIRE ==========

        // createForm() utilise RegistrationFormType
        // Ce FormType définit les champs du formulaire :
        // - email
        // - pseudo
        // - plainPassword (ATTENTION : pas stocké en BD, juste pour hacher)
        // - conditions d'utilisation (checkbox)
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // ========== VALIDATION ET TRAITEMENT ==========

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * ÉTAPE 1 : Récupérer le mot de passe en clair
             * 
             * plainPassword vient du formulaire, JAMAIS stocké en BD
             * Il est utilisé UNE SEULE FOIS pour hacher
             */
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // ========== ÉTAPE 2 : Hacher le mot de passe ==========

            /**
             * hashPassword(user, plainPassword) :
             * - user : l'entité User (contient le pseudo, email, etc.)
             * - plainPassword : le mot de passe saisi par l'utilisateur
             * 
             * Retourne un hash SÉCURISÉ et IRRÉVERSIBLE (Argon2)
             * JAMAIS stocker le mot de passe en clair !
             */
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // ========== ÉTAPE 3 : Définir les propriétés obligatoires ==========

            // Date de création (pour l'audit/historique)
            $user->setCreatedAt(new DateTime());

            // Utilisateur actif par défaut (pas besoin de confirmation email)
            // (À adapter : vous pourriez vouloir une confirmation avant activation)
            $user->setIsActive(true);

            // ========== ÉTAPE 4 : Persister et flush ==========

            // persist() : prépare l'insertion
            $entityManager->persist($user);

            // flush() : EXÉCUTE le INSERT en BD
            // Après flush() : $user a un ID (généré par la BD)
            $entityManager->flush();

            /**
             * OPTIONNEL : Envoyer un email de confirmation
             * $this->sendConfirmationEmail($user);
             */

            // ========== ÉTAPE 5 : Connexion automatique ==========

            /**
             * Security::login() connecte l'utilisateur AUTOMATIQUEMENT
             * 
             * Paramètres :
             * - $user : l'entité User à connecter
             * - 'form_login' : le firewall à utiliser (défini dans security.yaml)
             * - 'main' : le contexte de sécurité (à adapter selon config)
             * 
             * Avantage : UX excellent (pas besoin de se reconnecter après inscription)
             * IMPORTANT : À adapter selon votre configuration de sécurité
             */
            return $security->login($user, 'form_login', 'main');
        }

        // ========== AFFICHAGE DU FORMULAIRE ==========

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}