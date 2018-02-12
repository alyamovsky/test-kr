<?php
declare(strict_types=1);


namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;

class TwitterParser
{
    /** @var Crawler */
    private $crawler;

    /**
     * TwitterParser constructor.
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function getTweets()
    {
        $rawEntries = $this->parse();
        return $this->reformatEntries($rawEntries);
    }

    private function parse(): array
    {
        return $this->crawler->filter('.tweet')->each(function (Crawler $node) {
            return [
                'createdAt' => $node->filter('.tweet-timestamp')->attr('title'),
                'content' => $node->filter('.tweet-text')->text(),
            ];
        });
    }

    private function reformatEntries(array $entries): array
    {
        foreach ($entries as &$entry) {
            $entry['createdAt'] = $this->reformatDate($entry['createdAt']);
            $entry['content'] = $this->reformatContent($entry['content']);

            preg_match_all('~#\w+~', $entry['content'], $output, PREG_PATTERN_ORDER);
            $tags = $output[0];
            $entry['tags'] = !empty($tags) ? $tags : null;
        }

        return $entries;
    }

    private function reformatDate(string $date): string
    {
        $months = [
            'jan' => 'янв.',
            'feb' => 'февр.',
            'mar' => 'мар.',
            'apr' => 'апр.',
            'may' => 'май',
            'jun' => 'июн.',
            'jul' => 'июл.',
            'aug' => 'авг.',
            'sep' => 'сент.',
            'oct' => 'окт.',
            'nov' => 'нояб.',
            'dec' => 'дек.',
        ];

        $elements = explode(' ', $date);
        unset($elements[1], $elements[5]);

        $elements[3] = array_search($elements[3], $months);

        $date = implode(' ', $elements);

        return (string)$date;
    }

    private function reformatContent(string $content): string
    {
        $regex = "~((https?://|pic\.twitter)([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)~";
        $content = preg_replace($regex, ' ', $content);
        $content = trim($content, " \t\n\r\0\xB0\xA0");
        return $content;
    }
}