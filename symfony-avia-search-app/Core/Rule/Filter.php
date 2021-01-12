<?php


namespace App\Core\Rule;


class Filter
{
    const DEST_TYPE_DOMESTIC = 'Domestic';
    const DEST_TYPE_BOTH = 'Both';
    const DEST_TYPE_INTERN = 'International';

    public $airline = null;
    public $destType = null;
    public $departFrom = null;
    public $departTo = null;
    public $ticketFrom = null;
    public $ticketTo = null;
    public $operatedBy = null;
    public $platingCarrier = null;
    public $additionalCarrier = null;
    public $cabinClass = null;
    public $bookingClass = null;
    public $paxType = null;
    public $originEmpty = true;
    public $originCountry = null;
    public $originAirportCode = null;
    public $originContinent = null;
    public $destinationEmpty = true;
    public $destinationCountry = null;
    public $destinationAirportCode = null;
    public $destinationContinent = null;
    public $flightNumber = null;
}