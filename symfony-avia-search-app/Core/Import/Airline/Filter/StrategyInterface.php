<?php


namespace App\Core\Import\Airline\Filter;


use App\Entity\Airline;

interface StrategyInterface
{
    function filter(Airline $airline): bool;
}