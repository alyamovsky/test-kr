<?php
declare(strict_types=1);

namespace App\Parser;

use Goutte\Client as Goutte;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

/**
 * A Goutte wrapper
 */
class Client
{
    /** @var string */
    private $url;

    /** @var Goutte */
    private $client;

    /**
     * Client constructor.
     * @param string $url
     * @param array $cookies
     */
    public function __construct(string $url, array $cookies = [])
    {
        $this->url = $url;
        $this->client = new Goutte();

        if ($cookies) {
            $guzzleClient = new GuzzleClient(['cookies' => $this->setCookies($cookies)]);
            $this->client->setClient($guzzleClient);
        }
    }

    /**
     * @return Crawler
     */
    public function getCrawler(): Crawler
    {
        return $this->client->request('GET', $this->url);
    }

    /**
     * @param array $cookies
     * @return CookieJar
     */
    private function setCookies(array $cookies): CookieJar
    {
        return CookieJar::fromArray($cookies, parse_url($this->url, PHP_URL_HOST));
    }
}