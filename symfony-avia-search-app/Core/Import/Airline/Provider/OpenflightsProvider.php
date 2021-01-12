<?php


namespace App\Core\Import\Airline\Provider;


use App\Core\Import\Airline\Filter\StrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

class OpenflightsProvider extends BaseProvider
{
    private ?array $list;

    public function __construct(EntityManagerInterface $em, StrategyInterface $strategy)
    {
        $this->list = null;

        parent::__construct($em, $strategy);
    }

    public function import(): void
    {
        $arr = $this->getElements();

        $this->onImportStart();

        foreach ($arr as $item) {

            $icao = $this->normalize($item[4]);
            $iata = $this->normalize($item[3]);
            $name = $this->normalize($item[1]);
            $region = $this->normalize($item[6]);
            $this->updateCreate($icao, $iata, $name, $region);

            $this->stepIncrement();

            $this->onImportProgress();
        }

        $this->save();

        $this->onImportEnd();
    }

    private function getElements(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $handle = fopen($this->getUrl(), 'r');
        if ($handle === false) {
            throw new \Exception("Can't load: {$this->getUrl()}");
        }

        $this->list = [];
        fgetcsv($handle, 0, ",");
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $this->list[] = $row;
        }
        fclose($handle);

        return $this->list;
    }

    public function getElementsCount(): int
    {
        return count($this->getElements());
    }

    protected function getUrl(): string
    {
        return 'https://raw.githubusercontent.com/jpatokal/openflights/master/data/airlines.dat';
    }

    protected function getName(): string
    {
        return 'openflights.org';
    }

    /**
     * @param $value
     * @return string
     */
    private function normalize(string $value)
    {
        return str_replace(['\N', '-', 'N/A', 'n/a', '\\',"'",'=','-','+','^',':',';'], '', $value);
    }
}