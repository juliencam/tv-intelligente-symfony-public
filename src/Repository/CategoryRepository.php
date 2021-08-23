<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function filterByPosts(string $optionValue, $categoryId)
    {
        if ($optionValue != null) {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                "SELECT p
                FROM App\Entity\Post p
                JOIN p.categories c
                WHERE c.id = :categoryid
                ORDER BY p.createdAt ". $optionValue
            );
            $query->setParameter(':categoryid', $categoryId);
            //dd($query->getResult());
            return $query->getResult();
        }
    }

    public function filterByYoutuber(string $optionValue, $categoryId)
    {
        if ($optionValue != null) {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                "SELECT p
                FROM App\Entity\Post p
                JOIN p.categories c
                JOIN p.youtuber y
                WHERE c.id = :categoryid
                ORDER BY y.createdAt ". $optionValue
            );
            $query->setParameter(':categoryid', $categoryId);
            return $query->getResult();
        }
    }


    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
