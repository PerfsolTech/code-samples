<?php

namespace App\Controller;

use App\Core\Amadeus\Client;
use App\Core\Amadeus\Exception\CannotBookException;
use App\Core\Amadeus\Exception\CheckPriceException;
use App\Core\Amadeus\Exception\InvalidItineraryFormatException;
use App\Core\Amadeus\Exception\SearchException;
use App\Core\Amadeus\Model\BookRequest;
use App\Core\Amadeus\Model\SearchRequest;
use App\Entity\Location;
use App\Repository\LocationRepository;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/search", name="api_search", methods={"GET"})
     * @param Request $request
     * @param Client $client
     * @param SerializerInterface $serializer
     * @param LocationRepository $repository
     * @return JsonResponse
     * @throws InvalidItineraryFormatException
     */
    public function search(Request $request, Client $client, SerializerInterface $serializer, LocationRepository $repository)
    {
        try {
            $itinerary = $request->get('itn');
            $response = $client->search(SearchRequest::fromItinerary($itinerary, $repository));
        } catch (SearchException $ex) {
            return $this->failure(['error' => $ex->getMessage()]);
        }

        return $this->success($serializer, $response);
    }

    /**
     * @Route("/book", name="api_book", methods={"POST"})
     * @param Request $request
     * @param Client $client
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function book(Request $request, Client $client, SerializerInterface $serializer)
    {
        $searchKey = $request->get('sk');
        $key = $request->get('k');
        $data = $request->get('request');

        $searchResponse = $client->getSearchResponse($searchKey);
        if ($searchResponse == null) {
            // todo: throw exception?
        }
        $searchRequest = $searchResponse->getSearchRequest();
        $recommendation = $client->getRecommendation($searchResponse, $key);
        if ($recommendation == null) {
            // todo: throw exception?
        }
        /** @var BookRequest $bookRequest */
        $bookRequest = $serializer->deserialize($data, BookRequest::class, 'json');

        try {
            $client->updatePriceAndMiniRules($searchRequest, $recommendation);
            $client->updateCache($searchResponse, $recommendation);
            $client->book($recommendation, $bookRequest);
            $response = $this->success($serializer, []);
        } catch (CannotBookException $e) {
            $response = $this->failure(['error' => $e->getMessage()]);
        } catch (CheckPriceException $e) {
            $response = $this->failure(['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * @Route("/locations", name="api_locations", methods={"GET"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function locations(Request $request, SerializerInterface $serializer)
    {
        $value = $request->get('value');
        $result = $this->getDoctrine()
            ->getRepository(Location::class)->findByIataMunicipalityAirport($value);

        return $this->success($serializer, $result);
    }

    /**
     * @Route("/price", name="api_price", methods={"GET"})
     * @param Request $request
     * @param Client $client
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function checkPrice(Request $request, Client $client, SerializerInterface $serializer)
    {
        $searchKey = $request->get('sk');
        $key = $request->get('k');

        try {
            $searchResponse = $client->getSearchResponse($searchKey);
            if ($searchResponse == null) {
                // todo: throw exception?
            }
            $recommendation = $client->getRecommendation($searchResponse, $key);
            if ($recommendation == null) {
                // todo: throw exception?
            }
            $searchRequest = $searchResponse->getSearchRequest();
            $updatedRecommendation = clone $recommendation;

            $client->updatePriceAndMiniRules($searchRequest, $updatedRecommendation);
            $client->updateCache($searchResponse, $updatedRecommendation);
        } catch (CheckPriceException $ex) {
            return $this->failure(['error' => $ex->getMessage()]);
        }

        $discrepancy = $updatedRecommendation->getPrice()->getTotalAmount() !== $recommendation->getPrice()->getTotalAmount();
        $result = [
            'discrepancy' => $discrepancy,
            'recommendation' => $updatedRecommendation,
        ];

        return $this->success($serializer, $result);
    }

    private function success(SerializerInterface $serializer, $result): JsonResponse
    {
        return new JsonResponse($serializer->serialize($result, 'json'), 200, [], true);
    }

    private function failure(iterable $errors, int $status = 500): JsonResponse
    {
        $formattedErrors = [];

        foreach ($errors as $name => $error) {
            $isViolation = $error instanceof ConstraintViolation;
            $property = $isViolation ? (new CamelCaseToSnakeCaseNameConverter())->normalize($error->getPropertyPath()) : $name;

            $formattedErrors[$property][] = $isViolation ? $error->getMessage() : $error;
        }

        return new JsonResponse($formattedErrors, $status);
    }
}
