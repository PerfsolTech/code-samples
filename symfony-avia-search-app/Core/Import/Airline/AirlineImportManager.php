<?php


namespace App\Core\Import\Airline;


use App\Core\Import\Airline\Filter\ByICAOStrategy;
use App\Core\Import\Airline\Provider\OpenflightsProvider;
use App\Core\Import\Airline\Provider\ProviderInterface;
use App\Core\Import\Airline\Provider\WikiProvider;
use Doctrine\ORM\EntityManagerInterface;

class AirlineImportManager
{
    /**
     * @var ProviderInterface[]
     */
    private array $providers;

    public function __construct(EntityManagerInterface $em)
    {
        $this->providers = [
            new WikiProvider($em, new ByICAOStrategy()),
            new OpenflightsProvider($em, new ByICAOStrategy()),
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