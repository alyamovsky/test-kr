<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findMostPopular(int $period)
    {
        $threshold = date('Y-m-d H:i:s', time() - $period);
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb
            ->select('t')
            ->from('App:Tag', 't')
            ->andWhere('t.updatedAt > :threshold')
            ->addSelect('COUNT(a.id) as HIDDEN articles')
            ->join('t.articles', 'a')
            ->groupBy('t.id')
            ->addOrderBy('articles', 'DESC')
            ->addOrderBy('t.name')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();
    }
}
