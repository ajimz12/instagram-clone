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
     * Encuentra los posts de los usuarios seguidos por un usuario en particular.
     * Opcionalmente, puede incluir los posts del usuario en cuestión.
     */
    public function findFollowedUsersPosts(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->innerJoin('App\Entity\UserUser', 'uu', 'WITH', 'uu.userTarget = u AND uu.userSource = :user')
            ->where('uu.userSource = :user')
            ->setParameter('user', $user)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
