<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findByIataMunicipalityAirport(string $value)
    {
        $qb = $this->createQueryBuilder('location');

        $query = $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(location.iataCode)', 'LOWER(?1)'),
                    $qb->expr()->like('LOWER(location.municipality)', 'LOWER(?1)'),
                    $qb->expr()->like('LOWER(location.countryName)', 'LOWER(?1)'),
                    $qb->expr()->like('LOWER(location.airportName)', 'LOWER(?1)')
                )
            )
            ->andWhere($qb->expr()->eq('location.enabled', '?2'))
            ->andWhere($qb->expr()->neq('location.iataCode', '?3'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('location.airportType', '?4'),
                    $qb->expr()->eq('location.airportType', '?5')
                )
            )
            ->setParameter(1, "{$value}%")
            ->setParameter(2, true)
            ->setParameter(3, '')
            ->setParameter(4, 'large_airport')
            ->setParameter(5, 'medium_airport')
            ->setMaxResults(10)
            ->orderBy('location.airportType', 'ASC')
            ->orderBy('location.countryName', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    // /**
    //  * @return Iata[] Returns an array of Iata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Iata
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
