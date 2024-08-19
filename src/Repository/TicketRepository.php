<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\Ticket;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function add(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTicketsForConnectedUser($name, $username)
    {
        return $this->createQueryBuilder('t')
            // ->from(Ticket::class, 't')
            ->join('t.projet', 'p')
            ->join('p.' . $name, 'pp')
            ->where('pp.username = :username')
            ->setParameter('username', $username)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTachesByTickets($name, $username, $etat)
    {
        return $this->createQueryBuilder('t')
            ->join('t.tache', 'ta')
            ->join('ta.statut', 's')
            ->join('t.projet', 'p')
            ->join('p.' . $name, 'pp')
            ->where('ta.id IS NOT NULL')
            ->andWhere('s.etat = :etat')
            ->andWhere('pp.username = :username')
            ->setParameters([
                'etat' => $etat,
                'username' => $username
            ])
            ->getQuery()
            ->getResult();
    }

    public function findTicketNonTraite($name, $username)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.' . $name, 'pp')
            ->where('t.tache IS NULL')
            ->andWhere('pp.username = :username')
            ->setParameters([
                'username' => $username
            ])
            ->getQuery()
            ->getResult();
    }

    // public function findTachesWithoutTickets($name, $username)
    // {
    //     return $this->createQueryBuilder('t')
    //         ->join('t.taches', 'ta')
    //         ->join('t.projet', 'p')
    //         ->join('p.' . $name, 'pp')
    //         ->where('ta.ticket IS NULL')
    //         ->andWhere('pp.username = :username')
    //         ->setParameters([
    //             'username' => $username
    //         ])
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return Ticket[] Returns an array of Ticket objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ticket
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
