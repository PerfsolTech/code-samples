<?php


namespace App\Core\Rule;


use App\Entity\Commission;
use App\Repository\CommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RuleManager
{
    private CommissionRepository $commissionRepository;

    public function __construct(CommissionRepository $commissionRepository)
    {
        $this->commissionRepository = $commissionRepository;
    }

    /**
     * @return array|Commission[]
     */
    public function getCommission(Filter $filter): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->commissionRepository->createQueryBuilder('cm');

        $qb->where($qb->expr()->notLike('LOWER(cm.description)', 'LOWER(:desc)'))->setParameter('desc', "%fuel%");

//        if ($filter->airline) {
//            $qb->andWhere($qb->expr()->eq('cm.airline', ':airline'))
//                ->setParameter('airline', $filter->airline);
//        }
        if ($filter->destType) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('cm.destType', ':destType'),
                    $qb->expr()->eq('cm.destType', ':destTypeBoth')
                )
            )
                ->setParameter('destType', $filter->destType)
                ->setParameter('destTypeBoth', Filter::DEST_TYPE_BOTH);
        }
        if ($filter->departFrom) {
            $qb->andWhere(
                $qb->expr()->lte('cm.departFrom', ':departFrom')
            )
                ->setParameter('departFrom', $filter->departFrom);
        }
        if ($filter->departTo) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->gte('cm.departTo', ':departTo'),
                    $qb->expr()->isNull('cm.departTo')
                )
            )
                ->setParameter('departTo', $filter->departTo);
        }
        if ($filter->operatedBy) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('cm.operatedBy', ':operatedBy'),
                    $qb->expr()->eq('cm.operatedBy', ':operatedByEmpty')
                )
            )
                ->setParameter('operatedBy', $filter->operatedBy)
                ->setParameter('operatedByEmpty', '');
        }
        if ($filter->platingCarrier) {
            $qb->andWhere(
//                $qb->expr()->orX(
                $qb->expr()->eq('cm.platingCarrier', ':platingCarrier')
//                    $qb->expr()->eq('cm.platingCarrier', ':platingCarrierEmpty')
//                )
            )
                ->setParameter('platingCarrier', $filter->platingCarrier);
//                ->setParameter('platingCarrierEmpty', '');
        }
        if ($filter->additionalCarrier) {
            $qb->andWhere(
                $qb->expr()->in('cm.additionalCarrier', ':additionalCarrier')
            )
                ->setParameter('additionalCarrier', [$filter->additionalCarrier, 'ANY', 'N/A']);
        }
        if ($filter->cabinClass) {
            $qb->andWhere($qb->expr()->like('cm.cabinClass', ':cabinClass'))
                ->setParameter('cabinClass', "%{$filter->cabinClass}%");
        }
        if ($filter->originCountry) {
            $locArr = $this->getLocArr($filter->originContinent, $filter->originCountry, $filter->originAirportCode);
            if ($filter->originEmpty) {
                array_push($locArr, '');
            }
            $qb->andWhere($qb->expr()->in('cm.origin', ':origin'))->setParameter('origin', $locArr);
        }
        if ($filter->destinationCountry) {
            $locArr = $this->getLocArr(
                $filter->destinationContinent,
                $filter->destinationCountry,
                $filter->destinationAirportCode
            );
            if ($filter->destinationEmpty) {
                array_push($locArr, '');
            }
            $qb->andWhere($qb->expr()->in('cm.dest', ':destination'))->setParameter('destination', $locArr);
        }
        if ($filter->flightNumber) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->orX(
                        $qb->expr()->like('cm.permittedFlight', ':flightNumber'),
                        $qb->expr()->eq('cm.permittedFlight', ':empty')
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->like('cm.requiredFlight', ':flightNumber'),
                        $qb->expr()->eq('cm.requiredFlight', ':empty')
                    )
                )
            )->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->notLike('cm.notPermittedFlight', ':flightNumber'),
                    $qb->expr()->eq('cm.notPermittedFlight', ':empty')
                )
            )
                ->setParameter('flightNumber', "%{$filter->flightNumber}%")
                ->setParameter('empty', '');
        }

        return $qb->getQuery()->getResult();
    }


    private function getLocArr($continent, $country, $airport): array
    {
        $countryToContinent = [
            'BZ' => 'CENTRAL AMERICA',
            'CR' => 'CENTRAL AMERICA',
            'SV' => 'CENTRAL AMERICA',
            'GT' => 'CENTRAL AMERICA',
            'HN' => 'CENTRAL AMERICA',
            'NI' => 'CENTRAL AMERICA',
            'PA' => 'CENTRAL AMERICA',
            'BH' => 'MIDDLE EAST',
            'CY' => 'MIDDLE EAST',
            'EG' => 'MIDDLE EAST',
            'IR' => 'MIDDLE EAST',
            'IQ' => 'MIDDLE EAST',
            'IL' => 'MIDDLE EAST',
            'JO' => 'MIDDLE EAST',
            'KW' => 'MIDDLE EAST',
            'LB' => 'MIDDLE EAST',
            'OM' => 'MIDDLE EAST',
            'PS' => 'MIDDLE EAST',
            'QA' => 'MIDDLE EAST',
            'SA' => 'MIDDLE EAST',
            'SY' => 'MIDDLE EAST',
            'TR' => 'MIDDLE EAST',
            'AE' => 'MIDDLE EAST',
            'YE' => 'MIDDLE EAST',
            'NA' => 'NORTH AMERICA',
            'AF' => 'SOUTH ASIAN SUBCONTINENT',
            'BD' => 'SOUTH ASIAN SUBCONTINENT',
            'BT' => 'SOUTH ASIAN SUBCONTINENT',
            'IN' => 'SOUTH ASIAN SUBCONTINENT',
            'MV' => 'SOUTH ASIAN SUBCONTINENT',
            'NP' => 'SOUTH ASIAN SUBCONTINENT',
            'PK' => 'SOUTH ASIAN SUBCONTINENT',
            'LK' => 'SOUTH ASIAN SUBCONTINENT',
        ];
        $continentToContinent = [
            'SA' => 'SOUTH AMERICA',
            'EU' => 'EUROPE',
            'AF' => 'AFRICA',
            'AS' => 'ASIA',
            'OC' => 'SOUTH WEST PACIFIC',
        ];

        $iataToContinent = [
            'ECN' => 'MIDDLE EAST',
        ];

        $arr[] = $continent;
        $arr[] = $country;
        $arr[] = $airport;

        if (array_key_exists($continent, $continentToContinent)) {
            $arr[] = $continentToContinent[$continent];
        }

        if (array_key_exists($country, $countryToContinent)) {
            $arr[] = $countryToContinent[$country];
        }

        if (array_key_exists($airport, $iataToContinent)) {
            $arr[] = $iataToContinent[$airport];
        }

        return array_unique($arr);
    }

}