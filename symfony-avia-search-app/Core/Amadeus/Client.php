<?php


namespace App\Core\Amadeus;

use Amadeus\Client as AmadeusClient;
use Amadeus\Client\Params;
use Amadeus\Client\RequestOptions\Fare\InformativePricing\Passenger;
use Amadeus\Client\RequestOptions\Fare\InformativePricing\PricingOptions;
use Amadeus\Client\RequestOptions\Fare\MPDate;
use Amadeus\Client\RequestOptions\Fare\MPItinerary;
use Amadeus\Client\RequestOptions\Fare\MPLocation;
use Amadeus\Client\RequestOptions\Fare\MPPassenger;
use Amadeus\Client\RequestOptions\FareInformativeBestPricingWithoutPnrOptions;
use Amadeus\Client\RequestOptions\FareMasterPricerTbSearch;
use Amadeus\Client\RequestOptions\FarePricePnrWithBookingClassOptions;
use Amadeus\Client\RequestOptions\FarePricePnrWithLowerFaresOptions;
use Amadeus\Client\RequestOptions\Pnr\Element\Contact;
use Amadeus\Client\RequestOptions\Pnr\Element\FormOfPayment;
use Amadeus\Client\RequestOptions\Pnr\Itinerary;
use Amadeus\Client\RequestOptions\Pnr\Reference;
use Amadeus\Client\RequestOptions\Pnr\Segment\Air;
use Amadeus\Client\RequestOptions\Pnr\Traveller;
use Amadeus\Client\RequestOptions\PnrAddMultiElementsOptions;
use Amadeus\Client\RequestOptions\Ticket\PassengerReference;
use Amadeus\Client\RequestOptions\Ticket\Pricing;
use Amadeus\Client\RequestOptions\TicketCreateTstFromPricingOptions;
use Amadeus\Client\Result;
use Amadeus\Client\RequestOptions\PnrRetrieveOptions;
use App\Core\Amadeus\Exception\CannotBookException;
use App\Core\Amadeus\Exception\CheckPriceException;
use App\Core\Amadeus\Exception\EmptyCacheException;
use App\Core\Amadeus\Exception\SearchException;
use App\Core\Amadeus\Model\BookRequest;
use App\Core\Amadeus\Model\Order;
use App\Core\Amadeus\Model\Price;
use App\Core\Amadeus\Model\SearchRequest;
use App\Core\Amadeus\Model\SearchResponse;
use App\Core\Amadeus\Model\SearchResponse\FiltersBoundary;
use App\Core\Amadeus\Model\SearchResponse\Recommendation;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Fare;
use App\Core\Amadeus\Model\SearchResponse\Recommendation\Segment\Leg;
use App\Core\Rule\Filter;
use App\Core\Rule\RuleManager;
use App\Core\Util;
use App\Entity\Aircraft;
use App\Entity\Airline;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class Client
 *
 * @package App\App\Amadeus
 */
class Client
{
    private AmadeusClient $client;
    private EntityManagerInterface $em;
    private array $locations;
    private RuleManager $ruleManager;
    private LoggerInterface $logger;
    private array $aircraft;
    private CacheInterface $cache;
    private ExpressionLanguage $expressionLanguage;
    private int $nrOfRequestedResults;

    /**
     * @param RuleManager $ruleManager
     * @param EntityManagerInterface $em
     * @param CacheInterface $cache
     * @param LoggerInterface $amadeusLogger
     * @param string $officeId The Amadeus Office Id you want to sign in to - must be open on your WSAP
     * @param string $userId Also known as 'Originator' for Soap Header 1 & 2 WSDL's
     * @param string $passwordData **base 64 encoded** password
     * @param string $wsdl Path to WSDL file
     * @param int $nrOfRequestedResults
     */
    public function __construct(
        RuleManager $ruleManager,
        EntityManagerInterface $em,
        CacheInterface $cache,
        LoggerInterface $amadeusLogger,
        string $officeId,
        string $userId,
        string $passwordData,
        string $wsdl,
        int $nrOfRequestedResults
    ) {
        $this->locations = [];
        $this->aircraft = [];
        $this->em = $em;
        $params = new Params(
            [
                'authParams' => [
                    'officeId' => $officeId,
                    'userId' => $userId,
                    'passwordData' => base64_encode($passwordData),
                ],
                'sessionHandlerParams' => [
                    'soapHeaderVersion' => AmadeusClient::HEADER_V4,
                    'wsdl' => $wsdl,
//                    'stateful' => false,
//                    'enableTransactionFlowLink' => true,
                    'logger' => $amadeusLogger,
                ],
                'requestCreatorParams' => [
                    'receivedFrom' => 'JetSeeker',
                ],
            ]
        );
        $this->client = new AmadeusClient($params);
        $this->ruleManager = $ruleManager;
        $this->logger = $amadeusLogger;
        $this->cache = $cache;
        $this->expressionLanguage = new ExpressionLanguage();
        $this->nrOfRequestedResults = $nrOfRequestedResults;
    }

    /**
     * @throws SearchException
     */
    public function search(SearchRequest $request): SearchResponse
    {
        $response = new SearchResponse();
        $response->setSearchRequest($request);

        $passengers = [];
        if ($request->getPaxAdtCnt() > 0) {
            $passengers[] = new MPPassenger(['type' => MPPassenger::TYPE_ADULT, 'count' => $request->getPaxAdtCnt()]);
        }
        if ($request->getPaxChdCnt() > 0) {
            $passengers[] = new MPPassenger(['type' => MPPassenger::TYPE_CHILD, 'count' => $request->getPaxChdCnt()]);
        }
        if ($request->getPaxInfCnt() > 0) {
            $passengers[] = new MPPassenger(['type' => MPPassenger::TYPE_INFANT, 'count' => $request->getPaxInfCnt()]);
        }

        $itinerary = [];
        foreach ($request->getItinerary() as $leg) {
            $itinerary[] = new MPItinerary(
                [
                    'departureLocation' => new MPLocation(['city' => $leg->getDepartureIataCode()]),
                    'arrivalLocation' => new MPLocation(['city' => $leg->getArrivalIataCode()]),
                    'date' => new MPDate(['dateTime' => $leg->getDate()]),
                ]
            );
        }
        $opt = new FareMasterPricerTbSearch(
            [
                'nrOfRequestedResults' => $this->nrOfRequestedResults,
                'nrOfRequestedPassengers' => $request->getPaxCnt(),
                'passengers' => $passengers,
                'itinerary' => $itinerary,
                'doTicketabilityPreCheck' => true,
                'flightOptions' => [
                    FareMasterPricerTbSearch::FLIGHTOPT_PUBLISHED,
                    FareMasterPricerTbSearch::FLIGHTOPT_TICKET_AVAILABILITY_CHECK,
                ],
                'cabinClass' => $request->getCabinType(),
            ]
        );

        return $this->cache->get(
            $response->getKey(),
            function (ItemInterface $item) use ($response, $opt) {
                $item->expiresAfter(3600);

                /** @var Result $result */
                $this->client->setStateful(false);
                $result = $this->client->fareMasterPricerTravelBoardSearch($opt, ['returnXml' => true]);
                $this->client->setStateful(true);

                if ($result->status == 'ERR') {
                    throw new SearchException($result->messages[0]->text, $result->messages[0]->code);
                }

                $recommendations = $this->mapSearchResult($result->response);
                $response->setRecommendations($recommendations);
                $filtersBoundary = $this->makeFiltersBoundary($recommendations);
                $response->setFiltersBoundary($filtersBoundary);
                $this->applyRules($response);
                $this->applyPrice($response);

                return $response;
            }
        );
    }

    public function bookabilityCheck(Recommendation $recommendation)
    {

    }

    public function book(Recommendation $recommendation, BookRequest $bookRequest): Order
    {
        $order = new Order();
        $order->setRecommendation($recommendation);
        $order->setBookRequest($bookRequest);

        $itinerary = [];
        $paxCount = $bookRequest->paxCount();
        foreach ($recommendation->getSegments() as $segment) {

            $segments = [];
            foreach ($segment->getLegs() as $key => $leg) {
                $segments[] = new AmadeusClient\RequestOptions\Air\SellFromRecommendation\Segment(
                    [
                        'departureDate' => $leg->getDeparture()->getDatetime(),
                        'arrivalDate' => $leg->getArrival()->getDatetime(),
                        'from' => $leg->getDeparture()->getIata(),
                        'to' => $leg->getArrival()->getIata(),
                        'companyCode' => $segment->getMcx(),
                        'flightNumber' => $leg->getFlightNumber(),
                        'bookingClass' => $segment->getFares()[$key]->getBookingClass(),
                        // not proper way, key not correspond to actual fare
                        'nrOfPassengers' => $paxCount,
                        'statusCode' => AmadeusClient\RequestOptions\Air\SellFromRecommendation\Segment::STATUS_SELL_SEGMENT
//                        'flightTypeDetails' => AmadeusClient\RequestOptions\Air\SellFromRecommendation\Segment::INDICATOR_LOCAL_AVAILABILITY,
                    ]
                );
            }

            $itinerary[] = new AmadeusClient\RequestOptions\Air\SellFromRecommendation\Itinerary(
                [
                    'from' => $segment->getDeparture()->getIata(),
                    'to' => $segment->getArrival()->getIata(),
                    'segments' => $segments,
                ]
            );
        }

        $opt = new AmadeusClient\RequestOptions\AirSellFromRecommendationOptions(
            [
                'itinerary' => $itinerary,
            ]
        );
        $sfrResult = $this->client->airSellFromRecommendation($opt);
        if ($sfrResult->status == 'ERR') {
            throw new CannotBookException($sfrResult->messages[0]->text);
        }


        $trvls = [];
        $infantsKeys = [];
        $trvlNum = 1;
        foreach ($bookRequest->getPassengers() as $key => $passenger) {
            if (in_array($key, $infantsKeys)) {
                continue;
            }

            $orderPax = Order\Passenger::makeFromPassenger($passenger, $trvlNum);

            switch ($passenger->getType()) {
                case BookRequest\Passenger::PAX_TYPE_CHD:
                    $type = Traveller::TRAV_TYPE_CHILD;
                    break;
                case BookRequest\Passenger::PAX_TYPE_INF:
                    $type = Traveller::TRAV_TYPE_INFANT_WITH_SEAT;
                    break;
                case BookRequest\Passenger::PAX_TYPE_ADT:
                default:
                    $type = Traveller::TRAV_TYPE_ADULT;
                    break;
            }

            $trvlOpt = [
                'number' => $trvlNum++,
                'lastName' => $passenger->getLastName(),
                'firstName' => $passenger->getFirstName(),
                'travellerType' => $type,
                'dateOfBirth' => $passenger->getDateOfBirth(),
            ];

            if ($passenger->getType() == BookRequest\Passenger::PAX_TYPE_ADT) {
                foreach ($bookRequest->getPassengers() as $infKey => $inf) {
                    if ($inf->getType() == BookRequest\Passenger::PAX_TYPE_INF && !in_array($infKey, $infantsKeys)) {
                        $orderPax->setInfant(Order\Passenger::makeFromPassenger($inf, $trvlNum));

                        $infantsKeys[] = $infKey;
                        $trvlOpt['infant'] = new Traveller(
                            [
                                'number' => $trvlNum++,
                                'lastName' => $inf->getLastName(),
                                'firstName' => $inf->getFirstName(),
                                'travellerType' => Traveller::TRAV_TYPE_INFANT,
                                'dateOfBirth' => $inf->getDateOfBirth(),
                            ]
                        );
                        break;
                    }
                }
            }
            $trvls[] = new Traveller($trvlOpt);

            $order->addPassenger($orderPax);

            if (!empty($passenger->getPassportNumber())) {
                $lastName = str_replace('-', ' ', $passenger->getLastName());
                $firstName = str_replace('-', ' ', $passenger->getFirstName());
                $middleName = str_replace('-', ' ', $passenger->getMiddleName());
                $passport = "-P-AUS-{$passenger->getPassportNumber()}-AUS-{$passenger->getDateOfBirth()->format('dMy')}-{$passenger->getGender()}--{$lastName}-{$firstName}-{$middleName}";

                $pnrElements[] = new AmadeusClient\RequestOptions\Pnr\Element\ServiceRequest(
                    [
                        'type' => 'DOCS',
                        'status' => AmadeusClient\RequestOptions\Pnr\Element\ServiceRequest::STATUS_HOLD_CONFIRMED,
                        'quantity' => 1,
                        'freeText' => $passport,
                        'references' => [
                            new Reference(
                                [
                                    'type' => Reference::TYPE_PASSENGER_REQUEST,
                                    'id' => $trvlNum,
                                ]
                            ),
                        ],
                    ]
                );
            }
        }

        $ccNumber = str_replace(' ', '', $bookRequest->getCcNumber());
        $ccType = $this->getCcType($ccNumber);
        $pnrElements[] = new FormOfPayment(
            [
                'type' => FormOfPayment::TYPE_CREDITCARD,
                'creditCardType' => $ccType,
                'creditCardNumber' => $ccNumber,
                'creditCardExpiry' => str_replace(['/', ' '], '', $bookRequest->getCcExp()),
                'creditCardCvcCode' => $bookRequest->getCcCvc(),
                'creditCardHolder' => $bookRequest->getCcHolderName(),
            ]
        );
        $pnrElements[] = new Contact(
            [
                'type' => Contact::TYPE_EMAIL,
                'value' => $bookRequest->getEmail(),
            ]
        );
        $pnrElements[] = new Contact(
            [
                'type' => Contact::TYPE_PHONE_GENERAL,
                'value' => $bookRequest->getPhone(),
            ]
        );
        $pnrElements[] = new AmadeusClient\RequestOptions\Pnr\Element\Ticketing(
            [
                'ticketMode' => AmadeusClient\RequestOptions\Pnr\Element\Ticketing::TICKETMODE_OK,
            ]
        );

        $opt = new PnrAddMultiElementsOptions(
            [
                'travellers' => $trvls,
                'elements' => $pnrElements,
            ]
        );
        $this->client->pnrAddMultiElements($opt);

//        $fares = [];
//        $faresMap = [];
//        foreach ($recommendation->getSegments() as $segment) {
//            foreach ($segment->getFares() as $fare) {
//                $faresMap[$fare->getFareBasis()][] = $fare->getSegRef();
//            }
//        }
//        foreach ($faresMap as $fareBasicCode => $value) {
//            $references = [];
//            foreach (array_unique($value) as $segRef) {
//                $references[] = new AmadeusClient\RequestOptions\Fare\PricePnr\PaxSegRef(
//                    [
//                        'reference' => $segRef,
//                        'type' => AmadeusClient\RequestOptions\Fare\PricePnr\PaxSegRef::TYPE_SEGMENT,
//                    ]
//                );
//            }
//            $fares[] = new AmadeusClient\RequestOptions\Fare\PricePnr\FareBasis(
//                [
//                    'fareBasisCode' => $fareBasicCode,
//                    'references' => $references,
//                ]
//            );
//        }

        $farePriceRes = $this->client->farePricePnrWithBookingClass(
            new FarePricePnrWithBookingClassOptions(
                [
//                    'pricingsFareBasis' => $fares,
                    'overrideOptions' => [
                        FarePricePnrWithBookingClassOptions::OVERRIDE_FARETYPE_PUB,
                    ],
                    'currencyOverride' => $recommendation->getCurrency()->getCode(),
                    'validatingCarrier' => $recommendation->getSegments()[0]->getMcx(),
                ]
            )
        );

        if ($farePriceRes->status == 'ERR') {
            throw new CannotBookException($farePriceRes->messages[0]->text);
        }

        $pricings = [];
        $this->makeArray($farePriceRes->response->fareList);
        foreach ($farePriceRes->response->fareList as $fare) {

            $orderFare = new Order\Fare();
            $orderFare->setUniqueOfferReference($fare->offerReferences->offerIdentifier->uniqueOfferReference);
            $orderFare->setRefTst($fare->fareReference->uniqueReference);
            $orderFare->setTstIndicator($fare->pricingInformation->tstInformation->tstIndicator);
            $orderFare->setValidatingCarrierCode($fare->validatingCarrier->carrierInformation->carrierCode);
            if ($fare->lastTktDate->businessSemantic == 'LT') {
                $lastTktDate = new \DateTime();
                $lastTktDate->setDate(
                    $fare->lastTktDate->dateTime->year,
                    $fare->lastTktDate->dateTime->month,
                    $fare->lastTktDate->dateTime->day
                )->setTime(0, 0);
                if (property_exists($fare->lastTktDate->dateTime, 'hour') && property_exists(
                        $fare->lastTktDate->dateTime,
                        'minutes'
                    )) {
                    $lastTktDate->setTime($fare->lastTktDate->dateTime->hour, $fare->lastTktDate->dateTime->minutes);
                }
                $orderFare->setLastTktDate($lastTktDate);
            }
            foreach ($fare->fareDataInformation->fareDataSupInformation as $fareDataSupInformation) {
                if ($fareDataSupInformation->fareDataQualifier == '712') {
                    $orderFare->setTotalFareAmountFeeIncl(
                        new Money($fareDataSupInformation->fareAmount * 100, new Currency($fareDataSupInformation->fareCurrency))
                    );
                }
                if ($fareDataSupInformation->fareDataQualifier == 'TFT') {
                    $orderFare->setTotalFareAmountFeeExcl(
                        new Money($fareDataSupInformation->fareAmount * 100, new Currency($fareDataSupInformation->fareCurrency))
                    );
                }
                if ($fareDataSupInformation->fareDataQualifier == 'TOF') {
                    $orderFare->setTotalFee(
                        new Money($fareDataSupInformation->fareAmount * 100, new Currency($fareDataSupInformation->fareCurrency))
                    );
                }
            }

            $passengerReferences = [];
            $this->makeArray($fare->paxSegReference->refDetails);
            foreach ($fare->paxSegReference->refDetails as $refDetail) {
                // don't know why, but amadeus increase by one passenger number given in PnrAddMultiElementsOptions
                $refNumber = $refDetail->refNumber - 1;
                $order->setFareToPassenger($refNumber, $orderFare, $refDetail->refQualifier == 'PI');

                $passengerReferences[] = new PassengerReference(['id' => $refDetail->refNumber, 'type' => $refDetail->refQualifier]);
            }

            $pricings[] = new Pricing(
                [
                    'tstNumber' => $fare->fareReference->uniqueReference,
                    'passengerReferences' => $passengerReferences,
                ]
            );
        }

        $this->client->ticketCreateTSTFromPricing(
            new TicketCreateTstFromPricingOptions(
                [
                    'pricings' => $pricings,
                ]
            )
        );

        $opt = new PnrAddMultiElementsOptions(
            [
                'actionCode' => [
                    PnrAddMultiElementsOptions::ACTION_END_TRANSACT_RETRIEVE, //11
                    PnrAddMultiElementsOptions::ACTION_WARNING_AT_EOT,        //30
                ],
            ]
        );
        $pnrResult = $this->client->pnrAddMultiElements($opt);
        $order->setPnrLocator($pnrResult->response->pnrHeader->reservationInfo->reservation->controlNumber);
        $order->setReservedAt(
            \DateTime::createFromFormat(
                'dmy Hi',
                sprintf(
                    '%06d %04d',
                    $pnrResult->response->pnrHeader->reservationInfo->reservation->date,
                    $pnrResult->response->pnrHeader->reservationInfo->reservation->time
                )
            )
        );

        return $order;
    }

    private function getCcType(string $ccNumber): string
    {
        $schemes = [
            // American Express card numbers start with 34 or 37 and have 15 digits.
            'AX' => [
                '/^3[47][0-9]{13}$/',
            ],
//            // China UnionPay cards start with 62 and have between 16 and 19 digits.
//            // Please note that these cards do not follow Luhn Algorithm as a checksum.
//            'CHINA_UNIONPAY' => [
//                '/^62[0-9]{14,17}$/',
//            ],
            // Diners Club card numbers begin with 300 through 305, 36 or 38. All have 14 digits.
            // There are Diners Club cards that begin with 5 and have 16 digits.
            // These are a joint venture between Diners Club and MasterCard, and should be processed like a MasterCard.
            'DC' => [
                '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            ],
            // Discover card numbers begin with 6011, 622126 through 622925, 644 through 649 or 65.
            // All have 16 digits.
            'DS' => [
                '/^6011[0-9]{12}$/',
                '/^64[4-9][0-9]{13}$/',
                '/^65[0-9]{14}$/',
                '/^622(12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|91[0-9]|92[0-5])[0-9]{10}$/',
            ],
//            // InstaPayment cards begin with 637 through 639 and have 16 digits.
//            'INSTAPAYMENT' => [
//                '/^63[7-9][0-9]{13}$/',
//            ],
//            // JCB cards beginning with 2131 or 1800 have 15 digits.
//            // JCB cards beginning with 35 have 16 digits.
//            'JCB' => [
//                '/^(?:2131|1800|35[0-9]{3})[0-9]{11}$/',
//            ],
//            // Laser cards begin with either 6304, 6706, 6709 or 6771 and have between 16 and 19 digits.
//            'LASER' => [
//                '/^(6304|670[69]|6771)[0-9]{12,15}$/',
//            ],
            // Maestro international cards begin with 675900..675999 and have between 12 and 19 digits.
            // Maestro UK cards begin with either 500000..509999 or 560000..699999 and have between 12 and 19 digits.
            'TO' => [
                '/^(6759[0-9]{2})[0-9]{6,13}$/',
                '/^(50[0-9]{4})[0-9]{6,13}$/',
                '/^5[6-9][0-9]{10,17}$/',
                '/^6[0-9]{11,18}$/',
            ],
            // All MasterCard numbers start with the numbers 51 through 55. All have 16 digits.
            // October 2016 MasterCard numbers can also start with 222100 through 272099.
            'CA' => [
                '/^5[1-5][0-9]{14}$/',
                '/^2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12})$/',
            ],
//            // Payment system MIR numbers start with 220, then 1 digit from 0 to 4, then 12 digits
//            'MIR' => [
//                '/^220[0-4][0-9]{12}$/',
//            ],
//            // All UATP card numbers start with a 1 and have a length of 15 digits.
//            'UATP' => [
//                '/^1[0-9]{14}$/',
//            ],
            // All Visa card numbers start with a 4 and have a length of 13, 16, or 19 digits.
            'VI' => [
                '/^4([0-9]{12}|[0-9]{15}|[0-9]{18})$/',
            ],
            'E' => [
                '/^(4026|417500|4405|4508|4844|4913|4917)\d+$/',
            ],
        ];

        foreach ($schemes as $key => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $ccNumber)) {
                    return $key;
                }
            }
        }

        return '';
    }

    public function getSearchResponse(string $searchKey): ?SearchResponse
    {
        $response = null;
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem($searchKey);
        if ($cacheItem->isHit()) {
            /** @var SearchResponse $response */
            $response = $cacheItem->get();
        }

        return $response;
    }

    public function getRecommendation(SearchResponse $searchResponse, string $key): ?Recommendation
    {
        $recommendations = $searchResponse->getRecommendations() ?? [];
        $recommendation = null;
        foreach ($recommendations as $item) {
            if ($item->getKey() == $key) {
                $recommendation = $item;
                break;
            }
        }

        return $recommendation;
    }

    public function createTicket(string $recordLocator): void
    {
        $this->client->pnrRetrieve(
            new PnrRetrieveOptions(
                [

                ]
            )
        );
    }

    public function updateCache(SearchResponse $searchResponse, Recommendation $recommendation = null)
    {

        if ($recommendation != null) {
            foreach ($searchResponse->getRecommendations() as $key => $item) {
                if ($item->getKey() == $recommendation->getKey()) {
                    $arr = $searchResponse->getRecommendations();
                    $arr[$key] = $recommendation;
                    $searchResponse->setRecommendations($arr);
                    break;
                }
            }
        }


        /** @var CacheItem $cacheItem */
        $searchKey = $searchResponse->getKey();
        $cacheItem = $this->cache->getItem($searchKey);
        if ($cacheItem->isHit()) {
            $cacheItem->set($searchResponse);
            $this->cache->save($cacheItem);
        }
    }

    public function updatePriceAndMiniRules(SearchRequest $searchRequest, Recommendation $recommendation)
    {
        $passengers = [];
        if ($searchRequest->getPaxAdtCnt() > 0) {
            $start = 1;
            $end = $searchRequest->getPaxAdtCnt();
            $passengers[] = new Passenger(
                [
                    'tattoos' => range($start, $end),
                    'type' => Passenger::TYPE_ADULT,
                ]
            );
        }
        if ($searchRequest->getPaxChdCnt() > 0) {
            $start = $searchRequest->getPaxAdtCnt() + 1;
            $end = $start - 1 + $searchRequest->getPaxChdCnt();
            $passengers[] = new Passenger(
                [
                    'tattoos' => range($start, $end),
                    'type' => Passenger::TYPE_CHILD,
                ]
            );
        }
        if ($searchRequest->getPaxInfCnt() > 0) {
            // use tha same tattoos as  ADT coz INF can be sited only with ADT
            $start = 1;
            $end = $searchRequest->getPaxInfCnt();
            $passengers[] = new Passenger(
                [
                    'tattoos' => range($start, $end),
                    'type' => Passenger::TYPE_INFANT,
                ]
            );
        }

        $i = 1;
        $segmentsArr = [];
        foreach ($recommendation->getSegments() as $key => $segment) {
            foreach ($segment->getLegs() as $index => $leg) {
                $segmentsArr[] = new AmadeusClient\RequestOptions\Fare\InformativePricing\Segment(
                    [
                        'departureDate' => $leg->getDeparture()->getDatetime(),
                        'arrivalDate' => $leg->getArrival()->getDatetime(),
                        'from' => $leg->getDeparture()->getIata(),
                        'to' => $leg->getArrival()->getIata(),
                        'marketingCompany' => $leg->getMarketingCarrier(),
                        'operatingCompany' => $leg->getOperatingCarrier(),
                        'flightNumber' => $leg->getFlightNumber(),
                        'bookingClass' => $segment->getFares()[$index]->getBookingClass(),
                        // not proper way, key not correspond to actual fare
                        'groupNumber' => $key + 1,
                        'segmentTattoo' => $i++,
                    ]
                );
            }
        }

        $obj = new FareInformativeBestPricingWithoutPnrOptions(
            [
                'passengers' => $passengers,
                'segments' => $segmentsArr,
                'pricingOptions' => new PricingOptions(
                    [
                        'overrideOptions' => [
                            PricingOptions::OVERRIDE_FARETYPE_PUB,
                            PricingOptions::OVERRIDE_FARETYPE_UNI,
//                            PricingOptions::OVERRIDE_RETURN_LOWEST_AVAILABLE,
                        ],
                        'currencyOverride' => $recommendation->getCurrency()->getCode(),
                    ]
                ),
            ]
        );
        $result = $this->client->fareInformativeBestPricingWithoutPnr($obj);

        if ($result->status == 'ERR') {
            throw new CheckPriceException($result->messages[0]->text, $result->messages[0]->code);
        }

        $groupByMeasurementValue = [];
        $this->makeArray($result->response->mainGroup->pricingGroupLevelGroup);
        foreach ($result->response->mainGroup->pricingGroupLevelGroup as $key => $pricingGroupLevelGroup) {
            $this->makeArray($pricingGroupLevelGroup->passengersID->travellerDetails);
            $groupId = array_reduce(
                $pricingGroupLevelGroup->passengersID->travellerDetails,
                function ($carry, $travellerDetails) {
                    $carry .= $travellerDetails->measurementValue;

                    return $carry;
                },
                ''
            );

            if (property_exists($pricingGroupLevelGroup->fareInfoGroup->emptySegment, "fareDetails")) {
                $groupId .= $pricingGroupLevelGroup->fareInfoGroup->emptySegment->fareDetails->qualifier;
            }

            $groupByMeasurementValue[$groupId][$key] = $pricingGroupLevelGroup;
        }

        $cheapestPrice = PHP_INT_MAX;
        $cheapestPricingGroupLevelGroupArr = [];
        $idsArr = [];
        foreach ($groupByMeasurementValue as $group) {
            $cheapestPricingGroupLevelGroup = null;
            $cheapestKey = null;
            foreach ($group as $key => $pricingGroupLevelGroup) {
                $this->makeArray($pricingGroupLevelGroup->fareInfoGroup->fareAmount->otherMonetaryDetails);
                foreach ($pricingGroupLevelGroup->fareInfoGroup->fareAmount->otherMonetaryDetails as $otherMonetaryDetail) {
                    // get total fare
                    if ($otherMonetaryDetail->typeQualifier != '712') {
                        continue;
                    }
                    $priceInCents = $otherMonetaryDetail->amount * 100;
                    if ($priceInCents < $cheapestPrice) {
                        $cheapestKey = $key;
                        $cheapestPrice = $priceInCents;
                        $cheapestPricingGroupLevelGroup = $pricingGroupLevelGroup;
                    }
                }
            }
            $cheapestPricingGroupLevelGroupArr[] = $cheapestPricingGroupLevelGroup;
            $idsArr[] = $cheapestKey + 1;
        }

        $priceBaseFare = new Money(0, $recommendation->getCurrency());
        $priceTotalFare = new Money(0, $recommendation->getCurrency());

        foreach ($cheapestPricingGroupLevelGroupArr as $cheapestPricingGroupLevelGroup) {
            $useEquivalent = false;

            $numberOfPax = $cheapestPricingGroupLevelGroup->numberOfPax->segmentControlDetails->numberOfUnits;
            // get base fare
            $monetaryDetails = $cheapestPricingGroupLevelGroup->fareInfoGroup->fareAmount->monetaryDetails;
            if ($monetaryDetails->typeQualifier == 'B') {
                // checking if currency in recommendation equals to currency in fare
                if ($recommendation->getCurrency()->getCode() != $monetaryDetails->currency) {
                    // if not, will use equivalent amount, that was converted to recommendation currency
                    $useEquivalent = true;
                }
                // base fare provided in currency that used in recommendation
                if ($useEquivalent === false) {
                    $amountInCents = $monetaryDetails->amount * 100 * $numberOfPax;
                    $priceBaseFare = $priceBaseFare->add(new Money($amountInCents, $recommendation->getCurrency()));
                }
            }
            $this->makeArray($cheapestPricingGroupLevelGroup->fareInfoGroup->fareAmount->otherMonetaryDetails);
            foreach ($cheapestPricingGroupLevelGroup->fareInfoGroup->fareAmount->otherMonetaryDetails as $otherMonetaryDetail) {
                // get base fare
                // equivalent amount, that was converted to recommendation currency
                if ($useEquivalent && $otherMonetaryDetail->typeQualifier == 'E') {
                    $amountInCents = $otherMonetaryDetail->amount * 100 * $numberOfPax;
                    $priceBaseFare = $priceBaseFare->add(new Money($amountInCents, $recommendation->getCurrency()));
                }
                // get total fare; total fare always provided in currency that used in recommendation
                if ($otherMonetaryDetail->typeQualifier == '712') {
                    $amountInCents = $otherMonetaryDetail->amount * 100 * $numberOfPax;
                    $priceTotalFare = $priceTotalFare->add(new Money($amountInCents, $recommendation->getCurrency()));
                }
            }
        }

        $priceTax = $priceTotalFare->subtract($priceBaseFare);
        $recommendation->setPrice(new Price($priceBaseFare, $priceTax));
        $this->calcPrice($recommendation);

        $recommendation->setMiniRules($this->retrieveMiniRules($idsArr));
    }

    private function mapSearchResult($response): array
    {
        $result = [];
        $bagMap = [];

        $this->makeArray($response->recommendation);
        $this->makeArray($response->flightIndex);
        $this->makeArray($response->serviceFeesGrp);

        $currency = new Currency((string)$response->conversionRate->conversionRateDetail->currency);

        $serviceFeesGrp = null;
        foreach ($response->serviceFeesGrp as $serviceFeesGrp) {
            //Free baggage allowance
            if ($serviceFeesGrp->serviceTypeInfo->carrierFeeDetails->type == "FBA") {
                break;
            }
        }

        $this->makeArray($serviceFeesGrp->serviceCoverageInfoGrp);
        $this->makeArray($serviceFeesGrp->freeBagAllowanceGrp);

        foreach ($serviceFeesGrp->serviceCoverageInfoGrp as $serviceCoverageInfoGrp) {
            if ($serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail->refQualifier != 'F') {
                continue;
            }
            $index = $serviceCoverageInfoGrp->itemNumberInfo->itemNumber->number;
            $refNumber = $serviceCoverageInfoGrp->serviceCovInfoGrp->refInfo->referencingDetail->refNumber;
            foreach ($serviceFeesGrp->freeBagAllowanceGrp as $freeBagAllowanceGrp) {
                if ($freeBagAllowanceGrp->itemNumberInfo->itemNumberDetails->number != $refNumber) {
                    continue;
                }
                $quantity = $freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails->freeAllowance;
                $quantityCode = $freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails->quantityCode;
                $unitQualifier = property_exists($freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails, 'unitQualifier')
                    ? $freeBagAllowanceGrp->freeBagAllownceInfo->baggageDetails->unitQualifier : '';
                $bagMap[$index] = new Recommendation\BagAllowance($quantity, $quantityCode, $unitQualifier);
            }
        }

        foreach ($response->recommendation as $recommendation) {

            // convert amount to cents
            $priceTotal = new Money($recommendation->recPriceInfo->monetaryDetail[0]->amount * 100, $currency);
            $priceTax = new Money($recommendation->recPriceInfo->monetaryDetail[1]->amount * 100, $currency);
            $priceFare = $priceTotal->subtract($priceTax);

            $this->makeArray($recommendation->paxFareProduct);
            $this->makeArray($recommendation->segmentFlightRef);
            foreach ($recommendation->segmentFlightRef as $segmentFlightRef) {
                $segments = [];
                $bagAllowance = new Recommendation\BagAllowance();

                foreach ($segmentFlightRef->referencingDetail as $keyReferencingDetail => $referencingDetail) {
                    // S = Segment/service reference number
                    if ($referencingDetail->refQualifier == 'S') {
                        $flightIndex = $response->flightIndex[$keyReferencingDetail];
                        $refNumber = $referencingDetail->refNumber;

                        $this->makeArray($flightIndex->groupOfFlights);
                        $filterResult = array_filter(
                            $flightIndex->groupOfFlights,
                            function ($v) use ($refNumber) {
                                return $v->propFlightGrDetail->flightProposal[0]->ref == $refNumber; // todo: check the possibility that index 0 is not a refNumber
                            }
                        );
                        $groupOfFlights = $filterResult[array_key_first($filterResult)];

                        $legs = [];
                        $this->makeArray($groupOfFlights->flightDetails);
                        foreach ($groupOfFlights->flightDetails as $flightDetail) {
                            $flightInformation = $flightDetail->flightInformation;
                            $dateOfDeparture = $flightInformation->productDateTime->dateOfDeparture;
                            $timeOfDeparture = $flightInformation->productDateTime->timeOfDeparture;
                            $dateOfArrival = $flightInformation->productDateTime->dateOfArrival;
                            $timeOfArrival = $flightInformation->productDateTime->timeOfArrival;

                            $departure = $flightInformation->location[0];
                            $departureLocation = $departure->locationId;
                            $departureTerminal = property_exists($departure, 'terminal') ? $departure->terminal : '';
                            $departureEntityObj = $this->getLocation($departureLocation);

                            $arrival = $flightInformation->location[1];
                            $arrivalLocation = $arrival->locationId;
                            $arrivalTerminal = property_exists($arrival, 'terminal') ? $arrival->terminal : '';
                            $arrivalEntityObj = $this->getLocation($arrivalLocation);

                            $eft = '0000';
                            if ($flightInformation->attributeDetails->attributeType == 'EFT') {
                                $eft = $flightInformation->attributeDetails->attributeDescription;
                            }
                            $companyId = $flightInformation->companyId;

                            //equipmentType = UN/IATA code identifying type of aircraft (747,737,...)
                            $aircraftCode = $flightInformation->productDetail->equipmentType;
                            $aircraftName = $this->getAircraft($aircraftCode)->getTitle();

                            $departureDateTime = \DateTime::createFromFormat(
                                'dmy',
                                $dateOfDeparture,
                                new \DateTimeZone('UTC')
                            )->setTime((int)substr($timeOfDeparture, 0, 2), (int)substr($timeOfDeparture, 2, 2));
                            $arrivalDateTime = \DateTime::createFromFormat(
                                'dmy',
                                $dateOfArrival,
                                new \DateTimeZone('UTC')
                            )->setTime((int)substr($timeOfArrival, 0, 2), (int)substr($timeOfArrival, 2, 2));


                            $depObj = new Leg\Destination();
                            $depObj->setIata($departureLocation);
                            $depObj->setAirport($departureEntityObj->getAirportName());
                            $depObj->setMunicipality($departureEntityObj->getMunicipality());
                            $depObj->setCountry($departureEntityObj->getCountryIso());
                            $depObj->setContinent($departureEntityObj->getContinentIso());
                            $depObj->setDate($dateOfDeparture);
                            $depObj->setTime($timeOfDeparture);
                            $depObj->setDatetime($departureDateTime);
                            $depObj->setTerminal($departureTerminal);

                            $arvObj = new Leg\Destination();
                            $arvObj->setIata($arrivalLocation);
                            $arvObj->setAirport($arrivalEntityObj->getAirportName());
                            $arvObj->setMunicipality($arrivalEntityObj->getMunicipality());
                            $arvObj->setCountry($arrivalEntityObj->getCountryIso());
                            $arvObj->setContinent($arrivalEntityObj->getContinentIso());
                            $arvObj->setDate($dateOfArrival);
                            $arvObj->setTime($timeOfArrival);
                            $arvObj->setDatetime($arrivalDateTime);
                            $arvObj->setTerminal($arrivalTerminal);

                            $aircraft = new Leg\Aircraft();
                            $aircraft->setCode($aircraftCode);
                            $aircraft->setName($aircraftName);

                            $leg = new Leg();
                            $leg->setDeparture($depObj);
                            $leg->setArrival($arvObj);
                            $leg->setMarketingCarrier(
                                property_exists($companyId, 'marketingCarrier') ? $companyId->marketingCarrier : ''
                            );
                            $leg->setOperatingCarrier(
                                property_exists($companyId, 'operatingCarrier') ? $companyId->operatingCarrier : ''
                            );
                            $leg->setEft(sprintf("%sh %sm", substr($eft, 0, 2), substr($eft, 2, 2)));
                            $leg->setFlightNumber($flightInformation->flightOrtrainNumber);
                            $leg->setAircraft($aircraft);

                            $legs[] = $leg;
                        }

                        /** @var Leg $current */
                        foreach ($legs as $current) {
                            /** @var Leg $next */
                            $next = next($legs);

                            if ($next === false) {
                                break;
                            }

                            $al = $current->getArrival()->getIata();
                            $dl = $next->getDeparture()->getIata();
                            if ($al == $dl) {
                                /** @var \DateTime $adt */
                                $adt = $current->getArrival()->getDatetime();
                                /** @var \DateTime $ddt */
                                $ddt = $next->getDeparture()->getDatetime();

                                $int = $ddt->diff($adt);
                                $hours = Util::calculateHours($int);

                                $current->setLayover(sprintf("%sh %sm", $hours, $int->i));
                                $current->setLayoverMinutes(Util::calculateMinutes($int));
                            }
                        }


                        $eft = '0000'; // EFT = Elapse Flying Time
                        $mcx = ''; // MCX = Majority carrier
                        foreach ($groupOfFlights->propFlightGrDetail->flightProposal as $flightProposal) {
                            if (!property_exists($flightProposal, 'unitQualifier')) {
                                continue;
                            }
                            switch ($flightProposal->unitQualifier) {
                                case 'EFT':
                                    $eft = $flightProposal->ref;
                                    break;
                                case 'MCX':
                                    $mcx = $flightProposal->ref;
                                    break;
                            }
                        }

                        $fares = [];
                        foreach ($recommendation->paxFareProduct as $paxFareProduct) {
                            $this->makeArray($paxFareProduct->fareDetails);
                            $fareDetail = $paxFareProduct->fareDetails[$keyReferencingDetail];
                            $this->makeArray($fareDetail->groupOfFares);
                            foreach ($fareDetail->groupOfFares as $groupOfFares) {
                                $fare = new Fare();
                                $fare->setSegRef($fareDetail->segmentRef->segRef);
                                $fare->setFareBasis($groupOfFares->productInformation->fareProductDetail->fareBasis);
                                $fare->setPassengerType(
                                    $groupOfFares->productInformation->fareProductDetail->passengerType
                                );
                                $fare->setBookingClass($groupOfFares->productInformation->cabinProduct->rbd);
                                $fare->setCabinClass($groupOfFares->productInformation->cabinProduct->cabin);
                                $fare->setAvl($groupOfFares->productInformation->cabinProduct->avlStatus);

                                $fares[] = $fare;
                            }
                        }

                        $segment = new Segment();
                        $segment->setRef($refNumber);
                        $segment->setEft(new Segment\Eft(substr($eft, 0, 2), substr($eft, 2, 2)));
                        $segment->setMcx($mcx);
                        $segment->setLegs($legs);
                        $segment->setFares($fares);

                        $segments[] = $segment;
                    }

                    // B = Free bag allowance
                    if ($referencingDetail->refQualifier == 'B') {
                        $bagAllowance = $bagMap[$referencingDetail->refNumber] ?? $bagAllowance;
                    }
                }

                $rcm = new Recommendation();
                $rcm->setKey(Recommendation::makeKey($segments));
                $rcm->setItemNumberId($recommendation->itemNumber->itemNumberId->number);
                $rcm->setPrice(new Price($priceFare, $priceTax));
                $rcm->setCurrency($currency);
                $rcm->setSegments($segments);
                $rcm->setBagAllowance($bagAllowance);
                $result[] = $rcm;
            }

        }

        return $result;
    }

    public function pnrCancel(string $pnrLocator): void
    {
        $this->client->pnrCancel(
            new AmadeusClient\RequestOptions\PnrCancelOptions(
                [
                    'recordLocator' => $pnrLocator,
                    'cancelItinerary' => true,
                    'actionCode' => AmadeusClient\RequestOptions\PnrCancelOptions::ACTION_END_TRANSACT_RETRIEVE,
                ]
            )
        );
    }

//    private function applyRulesS(Segment $segment)
//    {
//        $filter = new Filter();
//        $filter->airline = $segment->getMcx();
//        $filter->destType = $segment->getDeparture()->getCountry() == $segment->getArrival()->getCountry()
//            ? Filter::DEST_TYPE_DOMESTIC : Filter::DEST_TYPE_INTERN;
//        $filter->departFrom = $segment->getDeparture()->getDatetime();
//        $filter->departTo = $segment->getArrival()->getDatetime();
////      $filter->ticketFrom = $leg['departure']['datetime']; //todo: maybe use current date
////        $filter->operatedBy = $segment->getOperatingCarrier();
//        $filter->platingCarrier = $segment->getMcx();
//        $filter->origin = $segment->getDeparture()->getCountry();
//        $filter->destination = $segment->getArrival()->getCountry();
//        $filter->flightNumber = $segment->getDeparture()->getFlightNumber();
//        $rules = $this->ruleManager->getCommission($filter);
//
////        if (count($rules) > 0) {
////            $this->logger->info('rule', [$leg, json_decode(json_encode($filter)), $rules]);
////        }
//
//        $leg->setCommissions($rules);    }


    private function applyRules(SearchResponse $response): void
    {
        $cabinClass = $response->getSearchRequest()->getCabinType();

        foreach ($response->getRecommendations() as $recommendation) {
            $rate = 0;
            $segment = $recommendation->getSegments()[0];
//            foreach ($recommendation->getSegments() as $segment) {
//                foreach ($segment->getLegs() as $leg) {
            $filter = new Filter();
//                    $filter->airline = $segment->getMcx();
            $filter->destType = $segment->getDeparture()->getCountry() == $segment->getArrival()->getCountry()
                ? Filter::DEST_TYPE_DOMESTIC : Filter::DEST_TYPE_INTERN;
            $filter->departFrom = $segment->getDeparture()->getDatetime();
            $filter->departTo = $segment->getDeparture()->getDatetime();
//      $filter->ticketFrom = $leg['departure']['datetime']; //todo: maybe use current date
//                    $filter->operatedBy = $leg->getOperatingCarrier();
            $filter->platingCarrier = $segment->getMcx();
//                    $filter->additionalCarrier = $leg->getOperatingCarrier();
            $filter->originCountry = $segment->getDeparture()->getCountry();
            $filter->originAirportCode = $segment->getDeparture()->getIata();
            $filter->originContinent = $segment->getDeparture()->getCountry();
            $filter->destinationCountry = $segment->getArrival()->getCountry();
            $filter->destinationAirportCode = $segment->getArrival()->getCountry();
            $filter->destinationContinent = $segment->getArrival()->getCountry();
            $filter->cabinClass = $cabinClass;
//                    $filter->flightNumber = $leg->getFlightNumber();

            $rules = $this->ruleManager->getCommission($filter);

//        if (count($rules) > 0) {
//            $this->logger->info('rule', [$leg, json_decode(json_encode($filter)), $rules]);
//        }
            if (count($rules) > 1) {
                $filter->originEmpty = false;
                $tmp = $this->ruleManager->getCommission($filter);
                if (count($tmp) == 1) {
                    $rules = $tmp;
                }
            }


            $recommendation->setCommissions($rules);

//                }
//            }

        }
    }

    private function getLocation(string $iata): Location
    {
        if (array_key_exists($iata, $this->locations)) {
            $location = $this->locations[$iata];
        } else {
            $location = $this->em->getRepository(Location::class)->findOneBy(['iataCode' => $iata]);
            if ($location == null) {
                $location = new Location();
                $location->setIataCode($iata);
            }

            $this->locations[$iata] = $location;
        }

        return $location;
    }

    private function getAircraft(string $aircraftCode): Aircraft
    {
        if (array_key_exists($aircraftCode, $this->aircraft)) {
            $aircraft = $this->aircraft[$aircraftCode];
        } else {
            $aircraft = $this->em->getRepository(Aircraft::class)->findOneBy(['iata' => $aircraftCode]);
            if ($aircraft == null) {
                $aircraft = new Aircraft();
                $aircraft->setTitle($aircraftCode);
                $aircraft->setModelName($aircraftCode);
            }

            $this->aircraft[$aircraftCode] = $aircraft;
        }

        return $aircraft;
    }

    /**
     * context-less (stateless).
     *
     * The context-less flow is the flow which contains context-less queries, that is,
     * the queries which don't require any prior nor later request to be functionally complete.
     *
     * An example of context-less request is a segment search request.
     * It is possible to search for segment, without needing later reservation.
     */
    private function setStateless(): self
    {
        $this->client->setStateful(false);

        return $this;
    }

    /**
     * context-full (statefull)
     *
     * The context-full flow is the flow which contains at least one context-full request, that is,
     * the request which requires prior or later request to be functionally complete.
     *
     * An example of context-full request is a Transactional Stored Ticket creation request.
     * To create TST, the pricing context must exist before.
     */
    private function setStateful(): self
    {
        $this->client->setStateful(true);

        return $this;
    }

    /**
     * @param $stdClass
     */
    private function makeArray(&$stdClass): void
    {
        if (!is_array($stdClass)) {
            $arr[] = $stdClass;
            $stdClass = $arr;
            unset($arr);
        }
    }

    private function applyPrice(SearchResponse $response): void
    {
        foreach ($response->getRecommendations() as $recommendation) {
            $this->calcPrice($recommendation);
        }
    }

    private function makeFiltersBoundary(array $recommendations): FiltersBoundary
    {
        $airlines = [];
        $airports = [];
        $stops = [];
        $efts = [];
        $dats = [];

        /** @var Recommendation $recommendation */
        foreach ($recommendations as $recommendation) {

            $stops[] = $recommendation->getStopsMax();

            foreach ($recommendation->getAirlines() as $iata) {
                if (array_key_exists($iata, $airlines)) {
                    continue;
                }
                if ($airline = $this->em->getRepository(Airline::class)->findOneBy(['iata' => $iata])) {
                    $airlines[$iata] = new FiltersBoundary\Airline($airline->getIata(), $airline->getName());
                }
            }
            foreach ($recommendation->getAirports() as $iata) {
                if (array_key_exists($iata, $airports)) {
                    continue;
                }
                if ($airport = $this->em->getRepository(Location::class)->findOneBy(['iataCode' => $iata])) {
                    $airports[$iata] = new FiltersBoundary\Airport($airport->getIataCode(), $airport->getAirportName());
                }
            }
            foreach ($recommendation->getEft() as $key => $value) {
                if (!array_key_exists($key, $efts)) {
                    $eft = new FiltersBoundary\Eft();
                    $eft->setKey($key);
                    $eft->setDeparture($value[0]);
                    $eft->setArrival($value[1]);

                    $efts[$key] = $eft;
                } else {
                    $eft = $efts[$key];
                }

                $eft->addMinutes($value[2]);
                $minutes = array_unique($eft->getMinutes(), SORT_NUMERIC);
                sort($minutes, SORT_ASC);
                $eft->setMinutes($minutes);
            }
            foreach ($recommendation->getDepartureArrivalTimes() as $key => $value) {
                if (!array_key_exists($key, $dats)) {
                    $dat = new FiltersBoundary\DepartureArrivalTimes();
                    $dat->setKey($key);
                    $dat->setDeparture($value[0]);
                    $dat->setArrival($value[1]);

                    $dats[$key] = $dat;
                } else {
                    $dat = $dats[$key];
                }

                $dat->addOutboundTimestamp($value[2]);
                $dat->addInboundTimestamp($value[3]);
                $inbounds = array_unique($dat->getInbounds(), SORT_NUMERIC);
                sort($inbounds, SORT_ASC);
                $dat->setInbounds($inbounds);
                $outbounds = array_unique($dat->getOutbounds(), SORT_NUMERIC);
                sort($outbounds, SORT_ASC);
                $dat->setOutbounds($outbounds);
            }
        }

        $fb = new FiltersBoundary();
        $fb->setAirlines($airlines);
        $fb->setAirports($airports);
        $fb->setStops(array_unique($stops, SORT_NUMERIC));
        $fb->setEft($efts);
        $fb->setDepartureArrivalTimes($dats);

        return $fb;
    }

    private function calcPrice(Recommendation $recommendation): void
    {
        $price = $recommendation->getPrice();
        $commissions = $recommendation->getCommissions();
        $result = 0;

        if (count($commissions) == 1) {
            $commission = $commissions[0];
            $rule = $commission->getPriceRule();
            if ($rule != null && !empty($rule->getRule())) {
                $result = $this->expressionLanguage->evaluate(
                    $rule->getRule(),
                    [
                        'fare' => $price->getFare()->getAmount(),
                        'agent_rate' => $commission->getAgentRate(),
                        'service_fee' => $commission->getServiceFee(),
                        'bsp_rate' => $commission->getBspRate(),
                        'rule_fixed' => $rule->getFixed(),
                        'rule_percent' => $rule->getPercent(),
                    ]
                );
            }
        }

        $price->setEvaluated(new Money($result, $recommendation->getCurrency()));
        if ($price->getEvaluated()->getAmount() == 0) {
            $price->setEvaluated($price->getFare());
        }
    }

    private function retrieveMiniRules(array $idsArr): array
    {
        $pricings = [];
        foreach ($idsArr as $id) {
            $pricings[] = new AmadeusClient\RequestOptions\MiniRule\Pricing(
                [
                    'id' => $id,
                    'type' => AmadeusClient\RequestOptions\MiniRule\Pricing::TYPE_FARE_RECOMMENDATION_NUMBER,
                ]
            );
        }
        $miniRulesResult = $this->client->miniRuleGetFromRec(
            new AmadeusClient\RequestOptions\MiniRuleGetFromRecOptions(
                [
                    'pricings' => $pricings,
                ]
            )
        );

        $miniRules = [];
        $this->makeArray($miniRulesResult->response->mnrByPricingRecord);
        foreach ($miniRulesResult->response->mnrByPricingRecord as $mnrByPricingRecord) {

            $this->makeArray($mnrByPricingRecord->mnrRulesInfoGrp);
            foreach ($mnrByPricingRecord->mnrRulesInfoGrp as $mnrRulesInfoGrp) {

                $this->makeArray($mnrRulesInfoGrp->mnrDateInfoGrp);
                foreach ($mnrRulesInfoGrp->mnrDateInfoGrp as $mnrDateInfoGrp) {
                    if ($mnrDateInfoGrp === null) {
                        continue;
                    }
                    $this->makeArray($mnrDateInfoGrp->dateInfo->dateAndTimeDetails);
                    foreach ($mnrDateInfoGrp->dateInfo->dateAndTimeDetails as $dateAndTimeDetails) {
                        $miniRules[] = new Recommendation\MiniRule(
                            $dateAndTimeDetails->qualifier,
                            \DateTime::createFromFormat(
                                'dMy Hi',
                                sprintf('%s %04d', $dateAndTimeDetails->date, $dateAndTimeDetails->time)
                            )->format('d-m-y H:i')
                        );
                    }
                }

                $this->makeArray($mnrRulesInfoGrp->mnrMonInfoGrp);
                foreach ($mnrRulesInfoGrp->mnrMonInfoGrp as $mnrMonInfoGrp) {
                    if ($mnrMonInfoGrp === null) {
                        continue;
                    }
                    $this->makeArray($mnrMonInfoGrp->monetaryInfo->monetaryDetails);
                    foreach ($mnrMonInfoGrp->monetaryInfo->monetaryDetails as $monetaryDetails) {
                        if ($monetaryDetails->amount == 0) {
                            continue;
                        }
                        $miniRules[] = new Recommendation\MiniRule(
                            $monetaryDetails->typeQualifier,
                            sprintf("%s %s", $monetaryDetails->amount, $monetaryDetails->currency)
                        );
                    }
                }

                $this->makeArray($mnrRulesInfoGrp->mnrRestriAppInfoGrp);
                foreach ($mnrRulesInfoGrp->mnrRestriAppInfoGrp as $mnrRestriAppInfoGrp) {
                    if ($mnrRestriAppInfoGrp === null) {
                        continue;
                    }
                    $this->makeArray($mnrRestriAppInfoGrp->mnrRestriAppInfo->statusInformation);
                    foreach ($mnrRestriAppInfoGrp->mnrRestriAppInfo->statusInformation as $statusInformation) {
                        $miniRules[] = new Recommendation\MiniRule($statusInformation->indicator, $statusInformation->action);
                    }
                }

            }

        }

        return $miniRules;
    }
}