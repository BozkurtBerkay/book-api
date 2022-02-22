<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function searchBookAndAuthor(string $bookName = null, string $authorName = null)
    {
        $qb = $this->_em->createQueryBuilder('c');
        return $qb
            ->select('b')
            ->from(Book::class, 'b')
            ->innerJoin(Author::class, 'a', Join::WITH, 'b.author = a.id')
            ->where($qb->expr()->orX(
                $qb->expr()->like('b.name', $bookName != null ? $qb->expr()->literal('%'.$bookName.'%'): $qb->expr()->literal('')),
                $qb->expr()->like('a.name', $authorName != null ? $qb->expr()->literal('%'.$authorName.'%'): $qb->expr()->literal(''))
            ))
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
