<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\QueryBuilder;
use App\DataModel\OrderSearchFilter;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOlder():?Order
    {
        return $this->createQueryBuilder('o')
                    ->orderBy('o.createdAt', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

    public function findByIds(array $ids):array 
    {
        return $this->createQueryBuilder('o')
                    ->where('o.id IN(:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getResult()
                    ;
    }

    public function findFilteredPaginated(OrderSearchFilter $searchFilter, Request $request, ?int $perPage = 10): PaginationInterface
    {
        $qb = $this->createQueryBuilder('o')
                    ;
        $this->filter($searchFilter, $qb);
        $query = $qb->getQuery();

        return $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $perPage /*limit per page*/
        );
    }

    private function filter(OrderSearchFilter $searchFilter, QueryBuilder $qb)
    {
        if($searchFilter->seen === false)
        {
            $qb->where('o.seen = 0');
        }

        if($searchFilter->sortBy === 'oldest')
        {
            $qb->orderBy('o.createdAt', 'ASC');
        }
        else
        {
            $qb->orderBy('o.createdAt', 'DESC');
        }
    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
