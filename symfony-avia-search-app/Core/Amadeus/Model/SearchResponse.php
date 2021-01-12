<?php


namespace App\Core\Amadeus\Model;


use App\Core\Amadeus\Model\SearchResponse\FiltersBoundary;
use App\Core\Amadeus\Model\SearchResponse\Recommendation;
use JMS\Serializer\Annotation as JMS;

class SearchResponse
{
    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchRequest")
     */
    private ?SearchRequest $searchRequest = null;
    /**
     * @var Recommendation[]|array
     * @JMS\Type(name="array<App\Core\Amadeus\Model\SearchResponse\Recommendation>")
     */
    private ?array $recommendations = null;

    /**
     * @JMS\Type(name="App\Core\Amadeus\Model\SearchResponse\FiltersBoundary")
     */
    private ?FiltersBoundary $filtersBoundary = null;

    public function getKey()
    {
        return $this->searchRequest->getKey();
    }

    /**
     * @return null|array|Recommendation[]
     */
    public function getRecommendations(): ?array
    {
        return $this->recommendations;
    }

    /**
     * @param Recommendation[]|array $recommendations
     */
    public function setRecommendations(array $recommendations): void
    {
        $this->recommendations = $recommendations;
    }

    public function getSearchRequest(): ?SearchRequest
    {
        return $this->searchRequest;
    }

    public function setSearchRequest(SearchRequest $searchRequest): void
    {
        $this->searchRequest = $searchRequest;
    }

    /**
     * @return FiltersBoundary|null
     */
    public function getFiltersBoundary(): ?FiltersBoundary
    {
        return $this->filtersBoundary;
    }

    /**
     * @param FiltersBoundary|null $filtersBoundary
     */
    public function setFiltersBoundary(?FiltersBoundary $filtersBoundary): void
    {
        $this->filtersBoundary = $filtersBoundary;
    }
}