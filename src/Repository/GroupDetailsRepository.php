<?php

namespace App\Repository;

use App\Entity\GroupDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GroupDetailsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GroupDetails::class);
    }
    /**
     * @param $username
     * @return boolean
     */
    public function findByGroup($groupName = null)
    {
        //converting the
        $returnResult = false;
        $whereCondition = "p.groupName = '" . $groupName . "'";
        if($groupName == null) return $returnResult;
        else{
            $qb = $this->createQueryBuilder('p')
                       ->where("$whereCondition");
            $queryObject = $qb->getQuery()->getResult();
            $returnResult = $queryObject;
        }
       return $returnResult;
    }
}