<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
    /**
     * @param $username
     * @return boolean
     */
    public function findByIsAdmin($username = null)
    {
        $returnResult   = false;
        $whereCondition = "p.username = '" . $username . "'";
        if($username == null) return $returnResult;
        else{
            $qb = $this->createQueryBuilder('p')
                       ->where("$whereCondition");
            $returnResult = $queryObject = $qb->getQuery()->getResult();
        }
       return $returnResult;
    }

    public function findByUsername($username = null)
    {
        $returnResult   = false;
        $whereCondition = "p.username = '" . $username . "'"; 
        // echo $username;
        if($username == null) return $returnResult;
        else{
            $qb = $this->createQueryBuilder('p')
                       ->where("$whereCondition")
                       ->andWhere("p.isDeleted = 0");
            $queryObject = $qb->getQuery()->getResult();
            $returnResult = $queryObject[0];
        }
        return $returnResult;   
    }
}
