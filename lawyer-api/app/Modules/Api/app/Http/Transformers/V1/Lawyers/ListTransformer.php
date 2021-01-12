<?php

namespace Api\Http\Transformers\V1\Lawyers;

use Api\Http\Transformers\V1\Lawyers\Profile\CompetenciesTransformer;
use App\Services\Image;
use League\Fractal\TransformerAbstract;

class ListTransformer extends TransformerAbstract
{
    private $imageService;

    /**
     * Attachments constructor.
     * @param $imageService
     */
    public function __construct(Image $imageService)
    {
        $this->imageService = $imageService;
    }

    public function transform($lawyer)
    {
        return [
            'id' => $lawyer->user_id,
            'name' => $lawyer->name,
            'avatar' => $this->imageService->getUrl($lawyer->avatar),
            'rating' => (float)$lawyer->rating,
            'address' => $lawyer->address,
            'latitude' => (float)$lawyer->latitude,
            'longitude' => (float)$lawyer->longitude,
            'created_at' => $lawyer->created_at->__toString(),
            'phone' => $lawyer->phone,
            'email' => $lawyer->email,
            'calls_allowed' => (bool)isset($lawyer->settings['calls_allowed']) ? $lawyer->settings['calls_allowed'] : true,
            'messages_allowed' => (bool)isset($lawyer->settings['messages_allowed']) ? $lawyer->settings['messages_allowed'] : true,
            'competencies' => $this->includeCompetencies($lawyer),
            'website' => $lawyer->website,
            'firm' => $lawyer->firm,
            'linkedin' => $lawyer->linkedin,
        ];

    }


    public function includeCompetencies($lawyer)
    {
        $competencies = [];
        if (count($lawyer->competencies)) {
            foreach ($lawyer->competencies as $competency) {
                $competencies[] = (new CompetenciesTransformer())->transform($competency);
            }
        }
        return $competencies;
    }

}