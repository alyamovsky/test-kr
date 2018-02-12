<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ArticleRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param int $page
     * @return Pagerfanta
     */
    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT a
                FROM App:Article a
                ORDER BY a.createdAt DESC
            ');

        return $this->createPaginator($query, $page);
    }

    /**
     * @param int $tagId
     * @param int $page
     * @return Pagerfanta
     */
    public function findByTag(int $tagId, int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT a
            FROM App:Article a
            join a.tags t
            where t.id = :id
            ORDER BY a.createdAt DESC
            ')->setParameter('id', $tagId);

        return $this->createPaginator($query, $page);
    }

    /**
     * @param Query $query
     * @param int $page
     * @return Pagerfanta
     */
    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Article::ITEMS_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
