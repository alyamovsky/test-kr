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
     * @param string $query
     * @param int $limit
     * @return Article[]
     */
    public function findBySearchQuery(string $query, int $limit = Article::ITEMS_PER_PAGE): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === count($searchTerms)) {
            return [];
        }

        $qb = $this->createQueryBuilder('a')
            ->join('a.tags', 't');

        foreach ($searchTerms as $key => $term) {
            $qb
                ->orWhere('a.content LIKE :t_' . $key)
                ->orWhere('t.name LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%');
        }

        return $qb
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $searchQuery
     * @return array
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', mb_strtolower($searchQuery)));

        return array_filter($terms, function ($term) {
            return 3 <= mb_strlen($term);
        });
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
