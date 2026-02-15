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


    public function findAllWithFilters(int $typeId = 0, int $modelId, ?string $city, bool $includeInactive = false): array
    {
        // ========== CRÉATION DU QUERY BUILDER ==========

        $qb = $this->createQueryBuilder('c')
            // Charger les relations (éviter les N+1 queries)
            ->leftJoin('c.type', 'typ')
            ->leftJoin('c.model', 'modl')
            ->leftJoin('c.media', 'm')
            ->leftJoin('c.adress', 'a')
            // GROUP BY pour éviter les doublons (à cause du JOIN sur votes)
            ->groupBy('c.id');

        if (!$includeInactive) {
            $qb->andWhere('c.isActive = :isActive')
                ->setParameter('isActive', true);
        }

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

        // ========== FILTRAGE PAR VILLE ==========

        if ($city) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $city);
        }

        // ========== EXÉCUTION ET RETOUR ==========

        return $qb->getQuery()->getResult();
    }


    public function findAvailableBoats(\DateTime $start, \DateTime $end): array
    {
        $qb = $this->createQueryBuilder('b');

        // On crée la sous-requête pour trouver les bateaux OCCUPÉS
        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('b2.id')
            ->from('App\Entity\Rental', 'r')
            ->join('r.boat', 'b2')
            ->where('r.rentalStart < :end')
            ->andWhere('r.rentalEnd > :start');

        // On filtre : Donne moi les bateaux dont l'ID n'est pas dans la liste des occupés
        return $qb->andWhere($qb->expr()->notIn('b.id', $subQuery->getDQL()))
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}
