<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Obtiene los posts de los usuarios seguidos por el usuario dado.
     */
    public function findFollowedUsersPosts(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u IN (:following)')
            ->setParameter('following', $user->getFollow()->toArray()) 
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
