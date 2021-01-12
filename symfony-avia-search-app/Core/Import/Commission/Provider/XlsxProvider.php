<?php


namespace App\Core\Import\Commission\Provider;


class XlsxProvider extends BaseProvider
{
    public function getFileExtension(): string
    {
        return "Xlsx";
    }
}