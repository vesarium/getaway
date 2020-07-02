<?php

namespace App\Repository;

use App\Entity\Bookings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method Bookings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bookings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bookings[]    findAll()
 * @method Bookings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookings::class);
    }
    
   public function getBookedAppointment($date, $days) {
       $endDate = new DateTime(date('Y-m-d', strtotime($date->format('Y-m-d'))));
       $endDate->modify('+'.$days.' day');
       return $this->createQueryBuilder('b')
            ->where('b.booking_date BETWEEN :start_date AND :end_date')
            ->setParameter('start_date', $date->format('Y-m-d'))
            ->setParameter('end_date', $endDate->format('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;
   }
}
