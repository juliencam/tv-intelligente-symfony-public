<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function filterByPosts(string $optionValue, $tagId)
    {
        if ($optionValue != null) {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                "SELECT p
                FROM App\Entity\Post p
                JOIN p.tags t
                WHERE t.id = :tagid
                ORDER BY p.createdAt ". $optionValue
            );
            $query->setParameter(':tagid', $tagId);
            return $query->getResult();
        }
    }
    // "SELECT c, p
    // FROM App\Entity\Post p
    // JOIN p.categories c
    // WHERE c.id = :categoryid
    // ORDER BY p.createdAt ". $optionValue

    /*
    public function findOneBySomeField($value): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
