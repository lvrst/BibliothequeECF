<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findLastEmprunts(int $num)
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date_emprunt', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEmprunteurId(int $id)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.emprunteur', 'b')
            ->andWhere('b.id LIKE :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByLivreId(int $id)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.livre', 'l')
            ->andWhere('l.id LIKE :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByReturnedBefore(string $date)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_retour < :str')
            ->setParameter('str', $date)
            ->orderBy('e.date_retour', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUnreturned()
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_retour is NULL')
            ->orderBy('e.date_emprunt', "ASC")
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByIdUnreturned(int $id)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.livre', 'l')
            ->andWhere('l.id LIKE :id')
            ->setParameter('id', $id)
            ->andWhere('e.date_retour is NULL')
            ->orderBy('e.date_emprunt', "ASC")
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Emprunt[] Returns an array of Emprunt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Emprunt
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
