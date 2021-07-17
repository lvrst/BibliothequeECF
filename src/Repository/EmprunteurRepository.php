<?php

namespace App\Repository;

use App\Entity\Emprunteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Emprunteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunteur[]    findAll()
 * @method Emprunteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmprunteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunteur::class);
    }
    public function findByFirstnameOrLastname($name)
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :name')
            ->orWhere('e.prenom LIKE :name')
            ->setParameter('name', "%{$name}%")
            ->orderBy('e.prenom', 'ASC')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUserId(int $id)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.user', 'u')
            ->andWhere('u.id LIKE :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByTel(string $num)
    {
        return $this->createQueryBuilder('e')
            ->where('e.tel LIKE :num')
            ->setParameter('num', "%{$num}%")
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDateCreation(string $date)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_creation < :str')
            ->setParameter('str', $date)
            ->orderBy('e.date_creation', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findActivity(bool $status)
    {
        return $this->createQueryBuilder('e')
            ->where('e.actif LIKE :val')
            ->setParameter('val', $status)
            ->orderBy('e.date_creation', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Emprunteur[] Returns an array of Emprunteur objects
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
    public function findOneBySomeField($value): ?Emprunteur
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
