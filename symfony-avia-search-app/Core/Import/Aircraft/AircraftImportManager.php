<?php


namespace App\Core\Import\Aircraft;


use App\Core\Import\Aircraft\Provider\AirlinecodesProvider;
use App\Core\Import\Aircraft\Provider\ProviderInterface;
use App\Core\Import\Aircraft\Provider\WikiProvider;
use Doctrine\ORM\EntityManagerInterface;

class AircraftImportManager
{
    /**
     * @var ProviderInterface[]
     */
    private array $providers;

    public function __construct(EntityManagerInterface $em)
    {
        $this->providers = [
            new WikiProvider($em),
            new AirlinecodesProvider($em),
        ];
    }

    public function subscribeOn($importProgressClosure = null, $importStartClosure = null, $importEndClosure = null): void
    {
        foreach ($this->providers as $provider) {
            if ($importProgressClosure != null) {
                $provider->subscribeOnImportProgress($importProgressClosure);
            }
            if ($importStartClosure != null) {
                $provider->subscribeOnImportStart($importStartClosure);
            }
            if ($importEndClosure != null) {
                $provider->subscribeOnImportEnd($importEndClosure);
            }
        }
    }

    public function import(): void
    {
        foreach ($this->providers as $provider) {
            $provider->import();
        }
    }
}