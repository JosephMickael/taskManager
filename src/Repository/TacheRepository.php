<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\TacheSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 *
 * @method Tache|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tache|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tache[]    findAll()
 * @method Tache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    public function save(Tache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterTasksByDev($name1, $username, $toFilter)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.' . $name1, 'n')
            ->join('t.developpeur', 'd')
            ->where('n.username = :username')
            ->andWhere('d.username = :usernameFilter')
            ->setParameter('username', $username)
            ->setParameter('usernameFilter', $toFilter)
            ->orderBy('t.id', 'DESC');
        // ->getQuery()
        // ->getResult();
    }

    public function filterTasksByStatut($name1, $username, $toFilter)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.' . $name1, 'n')
            ->join('t.statut', 's')
            ->where('n.username = :username')
            ->andWhere('s.etat = :toFilter')
            ->setParameter('username', $username)
            ->setParameter('toFilter', $toFilter)
            ->orderBy('t.id', 'DESC');
        // ->getQuery()
        // ->getResult();
    }

    public function filterByStatutForDev($statut, $username)
    {
        return $this->createQueryBuilder('t')
            ->join('t.statut', 's')
            ->join('t.developpeur', 'd')
            ->where('s.etat = :etat')
            ->andWhere('d.username = :username')
            ->setParameter('etat', $statut)
            ->setParameter('username', $username)
            ->orderBy('t.dateEchUser', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllTasksByConnectedUser($name, $username)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.' . $name, 'pp')
            ->where('pp.username = :username')
            ->setParameter('username', $username)
            ->orderBy('t.id', 'DESC');
        // ->getQuery()
        // ->getResult();
    }

    /**
     * Filtre par utilisateurs
     */
    public function filterDev($toFilter)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('t.developpeur', 'd')
            ->andWhere('d.username = :usernameFilter')
            ->setParameter('usernameFilter', $toFilter)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Filtre par statut
     */
    public function filterStatut($toFilter)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('t.statut', 's')
            ->andWhere('s.etat = :toFilter')
            ->setParameter('toFilter', $toFilter)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les taches avec statut pas encore vu (utilisé dans la notification)
     */
    public function findByTachesNonVue($username)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.chefprojet', 'cp')
            ->join('t.notifications', 'n')
            ->where('cp.username = :username')
            ->andWhere('n.etat = :etat')
            ->setParameter('username', $username)
            ->setParameter('etat', 'pas encore vu')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Nombre de taches pas encore terminé
     */
    public function findByTachesNonTermine($username)
    {
        return $this->createQueryBuilder('t')
            ->join('t.projet', 'p')
            ->join('p.chefprojet', 'cp')
            ->join('t.statut', 's')
            ->where('cp.username = :username')
            ->andWhere('s.etat != :etat')
            ->setParameter('username', $username)
            ->setParameter('etat', 'Terminé')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTachesNonTermineesAll()
    {
        return $this->createQueryBuilder('t')
            ->join('t.statut', 's')
            ->andWhere('s.etat != :etat')
            ->setParameter('etat', 'Terminé')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Tache[] Returns an array of Tache objects
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

    //    public function findOneBySomeField($value): ?Tache
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
