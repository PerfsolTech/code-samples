<?php


namespace App\Core\Import\Aircraft\Provider;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

class AirlinecodesProvider extends BaseProvider
{
    private ?Crawler $crawler;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);

        $this->crawler = null;
    }

    public function import(): void
    {
        $elements = $this->getElements();

        $this->onImportStart();

        /** @var \DOMElement $domElement */
        foreach ($elements as $domElement) {
            if (strpos($domElement->nodeValue, 'IATA') !== false || strpos($domElement->nodeValue, 'Code') !== false) {
                continue;
            }

            $tag = $domElement->getElementsByTagName('td');

            $iata = trim($tag->item(0)->nodeValue);
            $icao = trim($tag->item(1)->nodeValue);
            $model = str_replace(['all', 'models', 'Pax', 'pax', 'Freighter'], '', trim($tag->item(2)->nodeValue));

            $this->updateCreate($icao, $iata, $model, $model);

            $this->stepIncrement();

            $this->onImportProgress();
        }

        $this->onImportEnd();
    }

    private function getElements(): Crawler
    {
        if ($this->crawler == null) {
            $this->crawler = new Crawler($this->getContent());
            $this->crawler = $this->crawler->filterXPath("/html/body/table[4]/tr/td[2]/div/center/table/tr[count(.//td) = 4]");
        }

        return $this->crawler;
    }

    public function getElementsCount(): int
    {
        return $this->getElements()->count();
    }

    protected function getUrl(): string
    {
        return 'http://www.airlinecodes.co.uk/arctypes.asp';
    }

    protected function getName(): string
    {
        return 'airlinecodes.co.uk';
    }
}