<?php


namespace App\Core\Amadeus\Exception;


use Throwable;

class InvalidItineraryFormatException extends \Exception
{
    public function __construct($message = "Invalid itinerary format", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}