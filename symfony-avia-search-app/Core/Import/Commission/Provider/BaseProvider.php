<?php


namespace App\Core\Import\Commission\Provider;


use App\Entity\Commission;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class BaseProvider implements ProviderInterface
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function getSpreadsheet(UploadedFile $file): Spreadsheet
    {
        return IOFactory::createReader($this->getFileExtension())->load($file->getRealPath());
    }

    public function import(UploadedFile $file): void
    {
        $sh = $this->getSpreadsheet($file);
        $ws = $sh->getActiveSheet();

        $highestRow = $ws->getHighestRow();

        for ($row = 3; $row <= $highestRow; ++$row) {

            $airline = $ws->getCellByColumnAndRow(1, $row)->getValue();
            // check empty row
            if ($airline == null) {
                continue;
            }

            $obj = new Commission();

            $value = $ws->getCellByColumnAndRow(5, $row)->getValue();
            if (is_string($value)) {
                $departFrom = \DateTime::createFromFormat('d-M-Y', $value);
            } else {
                $departFrom = Date::excelToDateTimeObject($value);
            }
            $departTo = null;
            $value = $ws->getCellByColumnAndRow(6, $row)->getValue();
            // FUN - until further notice
            if ($value != 'UFN') {
                if (is_string($value)) {
                    $departTo = \DateTime::createFromFormat('d M Y', $value);
                } else {
                    $departTo = Date::excelToDateTimeObject($value);
                }
            }

            $value = $ws->getCellByColumnAndRow(7, $row)->getValue();
            if (is_string($value)) {
                $ticketFrom = \DateTime::createFromFormat('d-M-Y', $value);
            } else {
                $ticketFrom = Date::excelToDateTimeObject($value);
            }
            $ticketTo = null;
            $value = $ws->getCellByColumnAndRow(8, $row)->getValue();
            // FUN - until further notice
            if ($value != 'UFN') {
                if (is_string($value)) {
                    $ticketTo = \DateTime::createFromFormat('d M Y', $value);
                } else {
                    $ticketTo = Date::excelToDateTimeObject($value);
                }
            }

            $obj->setAirline($airline)
                ->setAirlineName($ws->getCellByColumnAndRow(2, $row)->getValue())
                ->setDescription($ws->getCellByColumnAndRow(3, $row)->getValue())
                ->setDestType($ws->getCellByColumnAndRow(4, $row)->getValue())
                ->setDepartFrom($departFrom)
                ->setDepartTo($departTo)
                ->setTicketFrom($ticketFrom)
                ->setTicketTo($ticketTo)
                ->setCodeshare($ws->getCellByColumnAndRow(9, $row)->getValue())
                ->setOperatedBy($ws->getCellByColumnAndRow(10, $row)->getValue())
                ->setCcPermited($ws->getCellByColumnAndRow(11, $row)->getValue() == 'Y')
                ->setCabinClass($ws->getCellByColumnAndRow(12, $row)->getValue())
                ->setBookingClass($ws->getCellByColumnAndRow(13, $row)->getValue())
                ->setBspRate((int)$ws->getCellByColumnAndRow(14, $row)->getValue())
                ->setAgentRate((int)$ws->getCellByColumnAndRow(15, $row)->getValue())
                ->setServiceFee((float)$ws->getCellByColumnAndRow(16, $row)->getValue())
                ->setApplyToAdult($ws->getCellByColumnAndRow(17, $row)->getValue() == 'Y')
                ->setApplyToChild($ws->getCellByColumnAndRow(18, $row)->getValue() == 'Y')
                ->setApplyToInfant($ws->getCellByColumnAndRow(19, $row)->getValue() == 'Y')
                ->setOrigin($ws->getCellByColumnAndRow(20, $row)->getValue())
                ->setDest($ws->getCellByColumnAndRow(21, $row)->getValue())
                ->setViceVersa($ws->getCellByColumnAndRow(22, $row)->getValue() == 'Y')
                ->setApplyToTourCode($ws->getCellByColumnAndRow(23, $row)->getValue() == 'Y')
                ->setTourCode($ws->getCellByColumnAndRow(24, $row)->getValue())
                ->setApplyToFareWithAdditionalCarriers($ws->getCellByColumnAndRow(25, $row)->getValue() == 'Y')
                ->setAdditionalCarrier($ws->getCellByColumnAndRow(26, $row)->getValue())
                ->setCarrierType($ws->getCellByColumnAndRow(27, $row)->getValue())
                ->setFareBasis($ws->getCellByColumnAndRow(28, $row)->getValue())
                ->setPlatingCarrier($ws->getCellByColumnAndRow(29, $row)->getValue())
                ->setPermittedFlight($this->separateFlights($ws->getCellByColumnAndRow(30, $row)->getValue()))
                ->setNotPermittedFlight($this->separateFlights($ws->getCellByColumnAndRow(31, $row)->getValue()))
                ->setRequiredFlight($this->separateFlights($ws->getCellByColumnAndRow(32, $row)->getValue()));

            $this->em->persist($obj);
        }
        $this->em->flush();
    }

    private function separateFlights(string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $flights = [];
        $arr = explode(',', $value);
        foreach ($arr as $range) {
            $minMaxArr = explode('-', $range);
            if (count($minMaxArr) == 1) {
                $flights[] = $minMaxArr[0];
            }
            if (count($minMaxArr) == 2) {
                $flights[] = implode(',', range($minMaxArr[0], $minMaxArr[1]));
            }
        }

        return implode(',', $flights);
    }
}