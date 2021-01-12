<?php


namespace App\Core\Import\Airline\Provider;


use Symfony\Contracts\EventDispatcher\Event;

class ImportEvent extends Event
{
    public const PROGRESS = 'airline_import.import_progress';
    public const START = 'airline_import.import_start';
    public const END = 'airline_import.import_end';
    private ProviderInterface $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
}