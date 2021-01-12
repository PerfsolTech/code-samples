<?php


namespace App\Core\Import\Aircraft\Provider;


use Symfony\Contracts\EventDispatcher\Event;

class ImportEvent extends Event
{
    public const PROGRESS = 'aircraft_import.import_progress';
    public const START = 'aircraft_import.import_start';
    public const END = 'aircraft_import.import_end';
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