<?php


namespace App\Core\Import\Commission\Provider;


class CsvProvider extends BaseProvider
{
    public function getFileExtension(): string
    {
        return "Csv";
    }
}