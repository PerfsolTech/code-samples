<?php


namespace App\Core\Import\Airline\Provider;

interface ProviderInterface
{
    function import(): void;

    function getElementsCount(): int;

    function getCurrentStep(): int;

    function subscribeOnImportProgress($closure): void;

    function subscribeOnImportStart($closure): void;

    function subscribeOnImportEnd($closure): void;
}