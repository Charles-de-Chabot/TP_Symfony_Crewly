<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adress;
use App\Form\ProfileType;
use App\Service\FileUploader;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * ProfileController - Gère le profil utilisateur
 * 
 * CONCEPTS CLÉS :
 * - IsGranted au niveau de la classe : s'applique à TOUTES les méthodes
 * - FileUploader : service custom pour gérer les uploads (au lieu de faire dans le contrôleur)
 * - Soft delete : on ne supprime pas vraiment, on désactive (voir UserController admin)
 */
#[Route('/profile')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     * 
     * @Route('', name: 'app_profile_show', methods: ['GET'])
     *   - '' : URL relative à la classe #[Route] = /profile
     * 
     * @return Response Vue du profil avec les infos utilisateur
     * 
     * PÉDAGOGIE :
     * - getUser() : récupère l'utilisateur connecté depuis la session
     * - render() passe $user au template
     * - Le template peut afficher les données de l'utilisateur
     */
    #[Route('', name: 'app_profile_show', methods: ['GET'])]
    public function show(RentalRepository $rentalRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $adress = $user->getAdress();
        $rentals = $rentalRepository->findBy(['user' => $user], ['rentalStart' => 'DESC']);


        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'adress' => $adress,
            'rentals' => $rentals,
        ]);
    }

    /**
     * Édite le profil de l'utilisateur
     * 
     * ROUTE : GET /profil/edit (affiche le formulaire)
     *         POST /profil/edit (traite le formulaire)
     * 
     * @param Request Contient les données POST
     * @param EntityManagerInterface Persiste les modifications
     * @param FileUploader Service pour uploader les fichiers
     * 
     * @return Response Vue du formulaire ou redirection après succès
     * 
     * PÉDAGOGIE - CYCLE DE GESTION DE FORMULAIRE :
     * 1. GET : createForm() + render() → affiche le formulaire vide
     * 2. POST : handleRequest() → remplit le formulaire avec les données
     * 3. isSubmitted() + isValid() → valide les données
     * 4. Traiter les fichiers (if uploadé)
     * 5. flush() → sauvegarde en BD
     * 6. Redirection vers une page de succès
     */
    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        // ========== RÉCUPÉRATION DE L'UTILISATEUR ==========

        /** @var User $user */
        $user = $this->getUser();

        // Si l'utilisateur n'a pas encore d'adresse, on en initialise une vide
        if (!$user->getAdress()) {
            $user->setAdress(new Adress());
        }

        // ========== CRÉATION DU FORMULAIRE ==========

        // createForm() :
        // - 1er param : classe ProfileType (définit les champs du formulaire)
        // - 2e param : entité à préremplir (possède les getters)
        $form = $this->createForm(ProfileType::class, $user);

        // handleRequest() :
        // - Pour GET : rien (le formulaire reste vide ou prérempli)
        // - Pour POST : remplit $user avec les données POST
        $form->handleRequest($request);

        // ========== VALIDATION ET TRAITEMENT ==========

        if ($form->isSubmitted() && $form->isValid()) {


            // ========== SAUVEGARDE EN BD ==========

            // On persiste l'adresse explicitement pour s'assurer qu'elle soit créée en BDD
            $em->persist($user->getAdress());

            // flush() : persiste toutes les modifications
            // IMPORTANT : si fichier uploadé, persister aussi AVANT les fichiers
            //            (car il faut que l'utilisateur existe en BD pour les relations)
            $em->flush();

            $this->addFlash('success', 'Votre profil a été mise à jour avec succès.');

            // Rediriger vers le profil après modification réussie
            // Évite la resoumission du formulaire si on rafraîchit la page
            return $this->redirectToRoute('app_profile_show');
        }

        // ========== AFFICHAGE DU FORMULAIRE ==========

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
