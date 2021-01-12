<?php


namespace App\Core\Import\Commission\Provider;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProviderInterface
{
    function getFileExtension(): string;

    function import(UploadedFile $file): void;
}