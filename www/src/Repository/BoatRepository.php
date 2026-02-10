<?php

namespace App\Repository;

use App\Entity\Boat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Boat>
 */
class BoatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boat::class);
    }

    public function findAllWithFilters(int $typeId = 0, int $modelId, string $city): array
    {
        // ========== CRÉATION DU QUERY BUILDER ==========

        $qb = $this->createQueryBuilder('c')
            // Charger les relations (éviter les N+1 queries)
            ->leftJoin('c.type', 'typ')
            ->leftJoin('c.model', 'modl')
            ->leftJoin('c.media', 'm')
            ->leftJoin('c.adress', 'a')
            // Condition de base : seulement les challenges actifs
            ->where('c.isActive = :isActive')
            ->setParameter('isActive', true)
            // Condition pour filter par ville
            ->andwhere('a.city = :city')
            ->setParameter('city', $city)
            // GROUP BY pour éviter les doublons (à cause du JOIN sur votes)
            ->groupBy('c.id');

             // ========== FILTRAGE PAR TYPE ==========

        if ($typeId > 0) {
            // Ajouter un filtre : seulement cette catégorie
            $qb->andWhere('typ.id = :typeId')
                ->setParameter('typeId', $typeId)
            ;
        }
            // ========== FILTRAGE PAR MODEL ==========

        if ($modelId > 0) {
            // Ajouter un filtre : seulement cette catégorie
            $qb->andWhere('modl.id = :modelId')
                ->setParameter('modelId', $modelId)
            ;
        }
            // ========== EXÉCUTION ET RETOUR ==========

        return $qb->getQuery()->getResult();

    }

}
