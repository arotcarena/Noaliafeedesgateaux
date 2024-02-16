<?php

namespace App\Repository;

use App\Config;
use App\Entity\Cake;
use Doctrine\ORM\QueryBuilder;
use App\DataModel\CakeSearchFilter;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Cake>
 *
 * @method Cake|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cake|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cake[]    findAll()
 * @method Cake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CakeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Cake::class);
    }

    public function save(Cake $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cake $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fullSpotlighted(): bool
    {
        return $this->countSpotlighted() >= Config::MAX_SPOTLIGHTED;
    }

    public function countSpotlighted(): int 
    {
        $result = $this->createQueryBuilder('c')
                        ->select('c.id')
                        ->where('c.spotlighted = 1')
                        ->getQuery()
                        ->getResult()
                        ;
        return count($result);
    }

    /**
     * @param int $limit
     * @param null|int $offset
     * @return array
     */
    public function findCurrents($limit, $offset = 0): array
    {
        return $this->createQueryBuilder('c')
                    ->select('c', 'p')
                    ->leftJoin('c.firstPicture', 'p')
                    ->orderBy('c.createdAt', 'DESC')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult()
                    ;
    }

    public function findAllSpotlighted(): array 
    {
        return $this->createQueryBuilder('c')
                    ->select('c', 'p')
                    ->leftJoin('c.firstPicture', 'p')
                    ->where('c.spotlighted = 1')
                    ->orderBy('c.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult()
                    ;
    }
    

    public function findFilteredPaginated(CakeSearchFilter $searchFilter, Request $request, ?int $perPage = 6): PaginationInterface
    {
        $qb = $this->createQueryBuilder('c')
                    ->select('c', 'p')
                    ->leftJoin('c.firstPicture', 'p')
                    ;
        $this->filter($searchFilter, $qb);
        if($searchFilter->spotlighted === true)
        {
            $qb->andWhere('c.spotlighted = true');
        }
        $query = $qb->getQuery();

        $pagination = $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $perPage /*limit per page*/
        );

        return $pagination;
    }

    public function countSpotlightedAndAll(CakeSearchFilter $searchFilter): array 
    {
        $qb = $this->createQueryBuilder('c')
                    ->select('c.id')
                    ->leftJoin('c.firstPicture', 'p')
                    ;
        $this->filter($searchFilter, $qb);
        
        $countAll = count(
            $qb->getQuery()
                ->getResult()
        );
        $countSpotlighted = count(
            $qb->andWhere('c.spotlighted = true')
                            ->getQuery()
                            ->getResult()
        );
        
        
        return [$countSpotlighted, $countAll];
    }

    public function findTitlesByQ(string $q, ?int $limit = 5)
    {
        $qb = $this->createQueryBuilder('c');
        $data = $qb
                    ->select('c.title as title')
                    ->orWhere('c.title LIKE :q')
                    ->orWhere('c.description LIKE :q')
                    ->andWhere('c.title IS NOT NULL')
                    ->setParameter('q', '%'.$q.'%')
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult()
                    ;
        return array_map(function($d) {
            return $d['title'];
        }, $data);
    }

    private function filter(CakeSearchFilter $searchFilter, QueryBuilder $qb)
    {
        if($searchFilter->q)
        {
            $qb->andWhere('c.title LIKE :q OR c.description LIKE :q')
                ->setParameter('q', '%'.$searchFilter->q.'%')
                ;
        }
        if($searchFilter->sortBy === 'oldest')
        {
            $qb->orderBy('c.createdAt', 'ASC');
        }
        elseif($searchFilter->sortBy === 'minVisited')
        {
            $qb->orderBy('c.countVisit', 'ASC');
        }
        elseif($searchFilter->sortBy === 'maxVisited')
        {
            $qb->orderBy('c.countVisit', 'DESC');
        }
        else
        {
            $qb->orderBy('c.createdAt', 'DESC');
        }
    }

//    /**
//     * @return Cake[] Returns an array of Cake objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cake
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
