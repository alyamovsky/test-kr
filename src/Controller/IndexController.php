<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @param int $page
     * @param ArticleRepository $articles
     * @param TagRepository $tags
     * @return Response
     *
     * @Route("/", defaults={"page": "1"}, name="index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="index_paginated")
     */
    public function index(int $page, ArticleRepository $articles, TagRepository $tags): Response
    {
        $latestArticles = $articles->findLatest($page);
        $popularTags = $tags->findMostPopular(Tag::RELEVANCE_TIME_IN_DAYS);

        return $this->render('news.html.twig', ['articles' => $latestArticles, 'tags' => $popularTags]);
    }

    /**
     * @param int $page
     * @param int $id
     * @param ArticleRepository $articles
     * @param TagRepository $tags
     * @return Response
     *
     * @Route("/tag/{id}", defaults={"page": "1"}, name="articles_by_tag")
     * @Route("/tag/{id}/page/{page}", requirements={"page": "[1-9]\d*"}, name="articles_by_tag_paginated")
     */
    public function showArticlesByTag(int $page, int $id, ArticleRepository $articles, TagRepository $tags): Response
    {
        $articlesByTag = $articles->findByTag($id, $page);
        $popularTags = $tags->findMostPopular(Tag::RELEVANCE_TIME_IN_DAYS);

        $tag = $tags->find($id);
        return $this->render('news.html.twig', ['articles' => $articlesByTag, 'tags' => $popularTags, 'title' => "News by tag $tag"]);
    }
}
