<?php

namespace Tests\Feature\Api\Lawyer\V1\Lawyers;

use App\Models\LawyerCase;
use App\Models\User;
use Tests\Feature\Api\AuthTrait;
use Tests\Feature\TestCase;

class GetProfileTest extends TestCase
{
    use AuthTrait;

    public function setUp()
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => \SeedJohnLawyer::class]);
        $this->setUser(User::TYPE_LAWYER);

        LawyerCase::select()->update(['user_id' => $this->user->id]);
    }


    public function test_success_get_profile()
    {
        $lawyer = User::lawyerProfile()->first();

        $response = $this->get(route('api.v1.lawyer.lawyers.get', ['lawyer_id' => $lawyer->id]), $this->getAuthHeader());
//        echo $response->baseResponse->getContent();
        $response->assertSuccessful()
            ->assertJsonStructure($this->profileStructure());

        $this->assertEquals($response->json()['data']['id'], $lawyer->id);
    }

    private function profileStructure()
    {
        return [
            'data' => [
                'id',
                'avatar',
                'rating',
                'address',
                'phone',
                'about',
                'website',
                'firm',
                'calls_allowed',
                'messages_allowed',
                'name',
                'latitude',
                'longitude',
                'is_review_added',
                'is_favorite',
                'assigned_case' => [

                ],
                'competencies' => [
                    [
                        'id',
                        'name'
                    ]
                ],
                'languages' => [
                    [
                        'id',
                        'name',
                        'level',
                    ]
                ],
                'cases' => [
                    'open',
                    'closed',
                ],
                'reviews' => [
                    [
                        'id',
                        'title',
                        'body',
                        'created_at',
                        'rating',
                    ],
                ],
                'created_at'
            ]
        ];
    }
}