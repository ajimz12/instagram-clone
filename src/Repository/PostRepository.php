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
     * @return Post[] Returns an array of Post objects by description
     */
    public function findByPost($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.description LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }



     * Obtiene los posts de los usuarios seguidos por el usuario dado.
     */
    public function findFollowedUsersPosts(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u IN (:following)')
            ->setParameter('following', $user->getFollow()->toArray()) // Obtener usuarios seguidos
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
  
  //    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
