<?php
declare(strict_types=1);


namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Parses tweets via DomCrawler
 */
class TwitterParser
{
    /** @var Crawler */
    private $crawler;

    /**
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @return array
     */
    public function getTweets()
    {
        $rawEntries = $this->parse();
        return $this->reformatEntries($rawEntries);
    }

    /**
     * @return array
     */
    private function parse(): array
    {
        return $this->crawler->filter('.tweet')->each(function (Crawler $node) {
            return [
                'createdAt' => $node->filter('.tweet-timestamp')->attr('title'),
                'content' => $node->filter('.tweet-text')->text(),
            ];
        });
    }

    /**
     * @param array $entries
     * @return array
     */
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

    /**
     * @param string $date
     * @return string
     */
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

        $elements[3] = in_array($elements[3], $months) ?
            array_search($elements[3], $months) :
            preg_replace('~\.~', '', $elements[3]);

        $date = implode(' ', $elements);

        return (string)$date;
    }

    /**
     * @param string $content
     * @return string
     */
    private function reformatContent(string $content): string
    {
        //remove links
        $content = preg_replace('~((https?://|pic\.twitter)([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)~', ' ', $content);
        //remove non-unicode symbols
        $content = preg_replace('~[\xc2\x80\xA0\xE2\x99]~', '', $content);
        $content = trim($content, " \t\n\r\0\xB0");
        return $content;
    }
}