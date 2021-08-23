<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{

    private $entityManager;

    public function __construct(ManagerRegistry $registry,EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Post::class);
        $this->entityManager = $entityManager;
    }

    public function search($words = null){
        $query = $this->createQueryBuilder('p');
        if ($words != null) {
            $query->where('MATCH_AGAINST(p.title) AGAINST (:words boolean)>0')
                  ->setParameter('words', $words);

            $result = $query->getQuery()->getResult();

            if (empty($result)) {
                //dd($result);
                $query->where('p.title LIKE :words')
                         ->setParameter('words', '%'.$words.'%');

                $result = $query->getQuery()->getResult();
            }

            return $result;
        }
    }

}
