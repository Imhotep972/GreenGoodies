<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @ return new reference Order  Returns a string (last reference with motif 'FA<YYYY><NNNN>')
     */
    public function getNewReference($value): ?string
    { // $sql -> SELECT reference FROM orders where reference like "FA2026%" ORDER BY reference DESC Limite 1
        $qb = $this->createQueryBuilder('o')
            ->select('o.reference')
            ->where('o.reference like :val')
            ->setParameter('val', $value.'%')
            ->orderBy('o.reference', 'DESC')
            ->setMaxResults(1)
       ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();


        if (\count($result))                        // il y a deja eu des factures
            return $result['reference'];
        else 
             return  $result['reference'];          // premiere facture avec ce motif

    }



//    /**
//     * @return Orders[] Returns an array of Orders objects
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

//    public function findOneBySomeField($value): ?Orders
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
