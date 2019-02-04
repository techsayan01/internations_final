<?php 

namespace App\Repository;

use App\Entity\UserGroupMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
// die("1111");

class UserGroupMapRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserGroupMap::class);
    }
    public function countByGroup($groupId = null){
        $returnResult = null;
        $whereCondition = "p.groupId = '" . $groupId . "'";
        $groupByCondition = "p.groupId";
        
        if($groupId == null) return $returnResult;
        else{
            $qb = $this->createQueryBuilder('p')
                    ->select("count(p.groupId) AS count")
                    ->where("$whereCondition")
                    ->andWhere("p.isDeleted = 0")
                    ->groupBy("$groupByCondition");

            $returnResult = $queryObject = $qb->getQuery()->getResult();
        }
       return $returnResult;
    }

    public function findByUserAndGroup($userId = null, $groupId = null){
        // die("1111");
        $returnResult = false;
        $whereCondition1 = "p.userId = '" . $userId . "'";
        $whereCondition2 = "p.groupId = '" . $groupId . "'";
        if($groupId == null || $userId == null) return $returnResult;
        else{
            $qb = $this->createQueryBuilder('p')
                    ->where("$whereCondition1")
                    ->andWhere("$whereCondition2")
                    ->andWhere("p.isDeleted = 0");

            $queryObject = $qb->getQuery()->getResult();
            $returnResult = $queryObject;
        }
        return $returnResult;
    }
}