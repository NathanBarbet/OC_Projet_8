<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findNotDone($userId)
    {
        return $this->createQueryBuilder('t')
            ->Where('t.isDone = 0')
            ->andWhere('t.userCreate = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findIsDone($userId)
    {
        return $this->createQueryBuilder('t')
            ->Where('t.isDone = 1')
            ->andWhere('t.userCreate = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNotDoneAdmin()
    {
        return $this->createQueryBuilder('t')
            ->Where('t.isDone = 0')
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findIsDoneAdmin()
    {
        return $this->createQueryBuilder('t')
            ->Where('t.isDone = 1')
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Task
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
