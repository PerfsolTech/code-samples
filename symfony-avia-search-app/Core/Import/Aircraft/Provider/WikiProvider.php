<?php


namespace App\Core\Import\Aircraft\Provider;


use App\Entity\Aircraft;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class WikiProvider extends BaseProvider
{
    private ?Crawler $crawler;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);

        $this->crawler = null;
    }

    function import(): void
    {
        $elements = $this->getElements();

        $this->onImportStart();

        /** @var \DOMElement $domElement */
        foreach ($elements as $domElement) {

            $tags = $domElement->getElementsByTagName('td');

            $nodeICAO = $tags->item(0);
            $icao = trim($nodeICAO->nodeValue);

            $nodeIATA = $tags->item(1);
            $iata = str_replace(['N/A', '[to be determined]', 'to be determined'], '', trim($nodeIATA->nodeValue));
            $iata = empty($iata) ? null : $iata;

            $nodeModel = $tags->item(2);
            $model = trim($nodeModel->nodeValue);
            if (strpos($model, 'deprecated') !== false) {
                continue;
            }
            $title = $nodeModel->firstChild->attributes->getNamedItem('title')->nodeValue;

            $this->updateCreate($icao, $iata, $model, $title);

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
        return 'https://en.wikipedia.org/wiki/List_of_aircraft_type_designators';
    }

    protected function getName(): string
    {
        return 'wikipedia';
    }
}