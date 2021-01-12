<?php


namespace App\Core\Import\Airline\Filter;


use App\Entity\Airline;

class ByICAOStrategy implements StrategyInterface
{
    private array $map;

    public function __construct()
    {
        $this->loadMap();
    }

    private function loadMap()
    {
        $file = __DIR__.'/icao.txt';
        foreach (file($file) as $line) {
            $this->map[] = trim($line);
        }
    }

    function filter(Airline $airline): bool
    {
        return in_array($airline->getIcao(), $this->map);
    }
}