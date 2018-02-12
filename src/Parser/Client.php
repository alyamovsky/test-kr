<?php
declare(strict_types=1);

namespace App\Parser;

use Goutte\Client as Goutte;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class Client
{
    /** @var string */
    private $url;

    /** @var Goutte */
    private $client;

    public function __construct(string $url, array $cookies = [])
    {
        $this->url = $url;
        $this->client = new Goutte();

        if ($cookies) {
            $guzzleClient = new GuzzleClient(['cookies' => $this->setCookies($cookies)]);
            $this->client->setClient($guzzleClient);
        }
    }

    public function getCrawler(): Crawler
    {
        return $this->client->request('GET', $this->url);
    }

    private function setCookies(array $cookies): CookieJar
    {
        return CookieJar::fromArray($cookies, parse_url($this->url, PHP_URL_HOST));
    }
}