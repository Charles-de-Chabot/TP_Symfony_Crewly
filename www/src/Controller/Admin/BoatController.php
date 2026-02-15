<?php

namespace App\Controller\Admin;

use App\Entity\Boat;
use App\Form\BoatType;
use App\Repository\AdressRepository;
use App\Repository\BoatRepository;
use App\Repository\ModelRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/boat')]
#[IsGranted('ROLE_ADMIN')]
final class BoatController extends AbstractController
{
    #[Route('/', name: 'app_admin_boat_index', methods: ['GET'])]
    public function index(
        BoatRepository $boatRepository,
        TypeRepository $typeRepository,
        ModelRepository $modelRepository,
        AdressRepository $adressRepository,
        Request $request
    ): Response {
        // 1. Extraction des paramètres
        $typeId = $request->query->getInt('type', 0);
        $modelId = $request->query->getInt('model', 0);
        $city = $request->query->get('city');

        $cityFilter = ($city === '0' || $city === '') ? null : $city;

        // 2. Logique pour filtrer la liste des MODÈLES
        if ($typeId > 0) {
            $modelsForSelect = $modelRepository->createQueryBuilder('m')
                ->innerJoin('App\Entity\Boat', 'b', 'WITH', 'b.model = m')
                ->where('b.type = :typeId')
                ->setParameter('typeId', $typeId)
                ->distinct()
                ->orderBy('m.label', 'ASC')
                ->getQuery()
                ->getResult();

            // Reset du modèle si incohérent avec le type
            $modelIds = array_map(fn($m) => $m->getId(), $modelsForSelect);
            if ($modelId > 0 && !in_array($modelId, $modelIds)) {
                $modelId = 0;
            }
        } else {
            $modelsForSelect = $modelRepository->findBy([], ['label' => 'ASC']);
        }

        // 3. Récupération des villes
        $cities = $adressRepository->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        // 4. Récupération des bateaux (incluant les inactifs pour l'admin)
        $boats = $boatRepository->findAllWithFilters($typeId, $modelId, $cityFilter, true);

        return $this->render('Admin/Boat/index.html.twig', [
            'boats' => $boats,
            'types' => $typeRepository->findAll(),
            'models' => $modelsForSelect,
            'cities' => $cities,
            'currentType' => $typeId,
            'currentModel' => $modelId,
            'currentCity' => $city ?? '0',
        ]);
    }

    #[Route('/new', name: 'app_admin_boat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boat = new Boat();
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $boat->setCreatedAt(new \DateTime());
            $boat->setUpdatedAt(new \DateTime());
            $entityManager->persist($boat);
            $entityManager->flush();

            $this->addFlash('success', 'Le bateau a été créé avec succès.');

            return $this->redirectToRoute('app_admin_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Boat/new.html.twig', [
            'boat' => $boat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_boat_show', methods: ['GET'])]
    public function show(Boat $boat): Response
    {
        return $this->render('Admin/Boat/show.html.twig', [
            'boat' => $boat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_boat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boat $boat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $boat->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Le bateau a été modifié avec succès.');

            return $this->redirectToRoute('app_admin_boat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/Boat/edit.html.twig', [
            'boat' => $boat,
            'form' => $form,
        ]);
    }
}
