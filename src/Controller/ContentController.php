<?php
declare(strict_types=1);

namespace App\Controller;

use App\Parser\Client;
use App\Parser\TwitterParser;
use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContentController is responsible for storing content in the database
 */
class ContentController extends AbstractController
{
    /**
     * @return Response
     *
     * @Route("/add_content", name="add_content")
     */
    public function addContentFromTwitter(): Response
    {
        $crawler = (new Client('https://twitter.com/CBCOlympics', ['lang' => 'ru']))->getCrawler();
        $parser = new TwitterParser($crawler);
        $entries = array_reverse($parser->getTweets());
        $entries = $this->removeAdded($entries);

        $count = 0;
        foreach ($entries as $entry) {
            $this->addArticle($entry);
            $count++;
        }

        return new Response("Successfully added $count new entries");
    }

    /**
     * @param array $entry
     */
    private function addArticle(array $entry): void
    {
        $article = new Article();
        $em = $this->getDoctrine()->getManager();
        $article->setCreatedAt(new \DateTime($entry['createdAt']));
        $article->setContent($entry['content']);

        if ($entry['tags']) {
            $tags = $this->setTags($entry['tags']);
            $article->addTags($tags);
        }

        $em->persist($article);
        $em->flush();
    }

    /**
     * @param array $entries
     * @return array
     */
    private function removeAdded(array $entries): array
    {
        /** @var Article $latestArticle */
        $latestArticle = $this->getDoctrine()->getRepository(Article::class)->findOneBy([], ['createdAt' => 'DESC']);

        return array_filter($entries, function ($item) use ($latestArticle) {
            return (strtotime($item['createdAt']) > $latestArticle->getCreatedAt()->gettimestamp());
        });
    }

    /**
     * @param array $entries
     * @return Tag[]|ArrayCollection
     */
    private function setTags(array $entries): ArrayCollection
    {
        $result = new ArrayCollection();
        foreach ($entries as $entry) {
            $tag = $this->getDoctrine()->getRepository(Tag::class)->findOneBy(['name' => $entry]) ?? new Tag();
            $tag->setName($entry);
            $result->add($tag);
        }

        return $result;
    }
}
