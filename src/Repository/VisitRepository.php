<?php

namespace App\Repository;

use App\DataModel\VisitSearchFilter;
use DateTime;
use DateTimeZone;
use App\Entity\Visit;
use App\Services\DateTimeHelper;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Visit>
 *
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry, 
        private PaginatorInterface $paginator,
        private DateTimeHelper $dateTimeHelper
        )
    {
        parent::__construct($registry, Visit::class);
    }

    public function save(Visit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Visit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOlder():?Visit
    {
        return $this->createQueryBuilder('v')
                    ->orderBy('v.visitedAt', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

    public function findFilteredPaginated(VisitSearchFilter $searchFilter, Request $request, $limit = 50): PaginationInterface
    {
        $qb = $this->createQueryBuilder('v');
        $this->filter($qb, $searchFilter);
        $query = $qb->getQuery();

        return $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $limit /*limit per page*/
        );
    }

    private function filter(QueryBuilder $qb, VisitSearchFilter $searchFilter)
    {
        //sortBy
        if($searchFilter->sortBy !== null && $searchFilter->sortBy === VisitSearchFilter::SORT_OLDEST)
        {
            $qb->orderBy('v.visitedAt', 'ASC');
        }
        else 
        {
            $qb->orderBy('v.visitedAt', 'DESC');
        }
        //page
        if($searchFilter->pageId !== null)
        {
            if($searchFilter->pageId === VisitSearchFilter::PAGE_HOME)
            {
                $qb->andWhere('v.visitedPageId = :pageId')
                    ->setParameter('pageId', VisitSearchFilter::PAGE_HOME)
                    ;
            }
            elseif($searchFilter->pageId === VisitSearchFilter::PAGE_CAKES)
            {
                $qb->andWhere('v.visitedPageId = :pageId')
                    ->setParameter('pageId', VisitSearchFilter::PAGE_CAKES)
                    ;
            }
        }
        //period
        if($searchFilter->period !== null)
        {
            if($searchFilter->period === VisitSearchFilter::PERIOD_TODAY)
            {
                $now = $this->dateTimeHelper->createImmutable();
                $qb->andWhere('v.visitedAt > :start')
                    ->andWhere('v.visitedAt < :end')
                    ->setParameter('start', $now->format('Y:m:d') . ' 00:00:00')
                    ->setParameter('end', $now->format('Y:m:d') . ' 23:59:59')
                    ;
            }
            elseif($searchFilter->period === VisitSearchFilter::PERIOD_LAST_WEEK)
            {
                $qb->andWhere('v.visitedAt > :period')
                    ->setParameter('period', $this->dateTimeHelper->createImmutable('now', ['sub' => 'P7D']))
                    ;
            }
            elseif($searchFilter->period === VisitSearchFilter::PERIOD_LAST_MONTH)
            {
                $qb->andWhere('v.visitedAt > :period')
                    ->setParameter('period', $this->dateTimeHelper->createImmutable('now', ['sub' => 'P1M']))
                    ;
            }
            elseif($searchFilter->period === VisitSearchFilter::PERIOD_LAST_THREE_MONTHS)
            {
                $qb->andWhere('v.visitedAt > :period')
                    ->setParameter('period', $this->dateTimeHelper->createImmutable('now', ['sub' => 'P3M']))
                    ;
            }
            elseif($searchFilter->period === VisitSearchFilter::PERIOD_LAST_YEAR)
            {
                $qb->andWhere('v.visitedAt > :period')
                    ->setParameter('period', $this->dateTimeHelper->createImmutable('now', ['sub' => 'P1Y']))
                    ;
            }
        }
       
    }

    public function countVisitsToday(): int 
    {
        $date_min = new DateTimeImmutable(
            (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('Y:m:d') . ' 00:00:00', 
            new DateTimeZone('Europe/Paris')
        );
        $date_max = new DateTimeImmutable(
            (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('Y:m:d') . ' 23:59:59', 
            new DateTimeZone('Europe/Paris')
        );

        return count(
            $this->createQueryBuilder('v')
                ->select('v.id')
                ->andWhere('v.visitedPageId = :home')
                ->setParameter('home', 'home')
                ->andWhere('v.visitedAt <= :date_max')
                ->andWhere('v.visitedAt >= :date_min')
                ->setParameter('date_max', $date_max->format('Y:m:d H:i:s'))
                ->setParameter('date_min', $date_min->format('Y:m:d H:i:s'))
                ->getQuery()
                ->getResult()
        );
    }

//    /**
//     * @return Visit[] Returns an array of Visit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Visit
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
