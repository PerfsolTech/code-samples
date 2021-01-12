<?php


namespace App\Core\Import\Airline\Provider;


use App\Core\Import\Airline\Filter\StrategyInterface;
use App\Entity\Airline;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class WikiProvider extends BaseProvider
{
    private ?Crawler $crawler;

    public function __construct(EntityManagerInterface $em, StrategyInterface $strategy)
    {
        $this->crawler = null;

        parent::__construct($em, $strategy);
    }

    function import(): void
    {
        $elements = $this->getElements();

        $this->onImportStart();

        /** @var \DOMElement $domElement */
        foreach ($elements as $domElement) {

            $tags = $domElement->getElementsByTagName('td');

            $iata = trim($tags->item(0)->nodeValue);
            if(strlen($iata) > 2) {
                $iata = substr($iata, 0, 2);
            }
            $icao = str_replace(['N/A', 'n/a'], '', trim($tags->item(1)->nodeValue));
            if(strlen($icao) > 2) {
                $icao = substr($icao, 0, 3);
            }
            $name = trim($tags->item(2)->nodeValue);
            $region = trim($tags->item(4)->nodeValue);

            $this->updateCreate($icao, $iata, $name, $region);

            $this->stepIncrement();

            $this->onImportProgress();
        }

        $this->onImportEnd();
    }

    private function getElements(): Crawler
    {
        if ($this->crawler == null) {
            $this->crawler = new Crawler($this->getContent());
            $this->crawler = $this->crawler->filterXPath("//*[@id=\"mw-content-text\"]/div/table/tbody/tr[td]");
        }

        return $this->crawler;
    }

    public function getElementsCount(): int
    {
        return $this->getElements()->count();
    }

    protected function getUrl(): string
    {
        return 'https://en.wikipedia.org/wiki/List_of_airline_codes';
    }

    protected function getName(): string
    {
        return 'wikipedia';
    }
}