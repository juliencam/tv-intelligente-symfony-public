<?php

namespace App\Repository;

use App\Entity\Youtuber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Youtuber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Youtuber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Youtuber[]    findAll()
 * @method Youtuber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YoutuberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Youtuber::class);
    }

    public function search($words = null){

        $query = $this->createQueryBuilder('y');
         if($words != null){

                $query->where('MATCH_AGAINST(y.name) AGAINST (:words boolean)>0')
                      ->setParameter('words', $words);

                $result = $query->getQuery()->getResult();

                if (empty($result)) {
                    //dd($result);
                    $query->where('y.name LIKE :words')
                          ->setParameter('words', '%'.$words.'%');

                    $result = $query->getQuery()->getResult();
                }
         }

        return $result;
    }


    // /**
    //  * @return Youtuber[] Returns an array of Youtuber objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Youtuber
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
