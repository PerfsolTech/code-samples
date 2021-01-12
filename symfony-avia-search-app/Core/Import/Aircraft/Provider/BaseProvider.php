<?php


namespace App\Core\Import\Aircraft\Provider;


use App\Entity\Aircraft;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;

abstract class BaseProvider implements ProviderInterface
{
    protected EntityManagerInterface $em;
    protected EventDispatcher $dispatcher;
    private int $step = 0;
    private int $batchSize = 100;
    private \Closure $closure;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->dispatcher = new EventDispatcher();
        $this->closure = function () {
            $this->save();
        };
        $this->closure->bindTo($this, $this);

        $this->subscribeOnImportEnd($this->closure);
    }

    public function __destruct()
    {
        $this->unsubscribeOnImportEnd($this->closure);
    }

    public abstract function import(): void;

    public function getCurrentStep(): int
    {
        return $this->step;
    }

    public function subscribeOnImportProgress($closure): void
    {
        $this->dispatcher->addListener(ImportEvent::PROGRESS, $closure);
    }

    public function unsubscribeOnImportProgress($closure): void
    {
        $this->dispatcher->removeListener(ImportEvent::PROGRESS, $closure);
    }

    public function subscribeOnImportStart($closure): void
    {
        $this->dispatcher->addListener(ImportEvent::START, $closure);
    }

    public function unsubscribeOnImportStart($closure): void
    {
        $this->dispatcher->removeListener(ImportEvent::START, $closure);
    }

    public function subscribeOnImportEnd($closure): void
    {
        $this->dispatcher->addListener(ImportEvent::END, $closure);
    }

    public function unsubscribeOnImportEnd($closure): void
    {
        $this->dispatcher->removeListener(ImportEvent::END, $closure);
    }

    private function makeEvent(): ImportEvent
    {
        return new ImportEvent($this);
    }

    protected function onImportProgress(): void
    {
        $this->dispatcher->dispatch($this->makeEvent(), ImportEvent::PROGRESS);
    }

    protected function onImportStart(): void
    {
        $this->dispatcher->dispatch($this->makeEvent(), ImportEvent::START);
    }

    protected function onImportEnd(): void
    {
        $this->dispatcher->dispatch($this->makeEvent(), ImportEvent::END);
    }

    protected abstract function getUrl(): string;

    protected abstract function getName(): string;

    protected function getContent(): string
    {
        $response = HttpClient::create()->request(
            'GET',
            $this->getUrl()
        );

        return $response->getContent();
    }

    /**
     * @param string $icao
     * @param string $iata
     * @param string $model
     * @param string $title
     */
    protected function updateCreate(string $icao, ?string $iata, string $model, string $title): void
    {
        $obj = $this->em->getRepository(Aircraft::class)->findOneBy(['icao' => $icao, 'iata' => $iata]);
        if ($obj == null) {
            $obj = new Aircraft();
        }
        $obj
            ->setIcao($icao)
            ->setIata($iata)
            ->setModelName($model)
            ->setTitle($title);

        $this->em->persist($obj);

        if ($this->getCurrentStep() % $this->batchSize === 0) {
            $this->save();
        }
    }

    protected function save()
    {
        $this->em->flush();
        $this->em->clear();
    }

    protected function stepIncrement(): void
    {
        ++$this->step;
    }
}