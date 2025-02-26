<?php

namespace one2cars;

use Symfony\Component\DomCrawler\Crawler;

class One2CarsParser
{
    private Crawler $crawler;

    public function __construct(string $html)
    {
        $this->crawler = new Crawler($html);
    }

    public function getJsonData(): string
    {
        return $this->crawler->filterXPath('//script[@type="application/ld+json"][1]')->innerText();
    }

    public function getMaxPage(): int
    {
        return $this->crawler->filterXPath('//a[@data-page][last()]')->attr('data-page', 1) + 1;
    }

}