<?php


namespace App\Core\Import\Commission;


use App\Core\Import\Commission\Provider\CsvProvider;
use App\Core\Import\Commission\Provider\ProviderInterface;
use App\Core\Import\Commission\Provider\XlsxProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommissionImportManager
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    public function __construct(EntityManagerInterface $em)
    {
        $this->providers = [
            new CsvProvider($em),
            new XlsxProvider($em),
        ];
    }

    public function import(UploadedFile $file): void
    {
        foreach ($this->providers as $provider) {
            $fileEx = mb_strtolower($file->getClientOriginalExtension());
            $providerEx = mb_strtolower($provider->getFileExtension());
            if ($fileEx == $providerEx) {
                $provider->import($file);
            }
        }
    }
}