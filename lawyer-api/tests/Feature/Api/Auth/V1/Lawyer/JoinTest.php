<?php

namespace Tests\Feature\Api\Auth\V1\Lawyer;

use App\Models\City;
use App\Models\Competency;
use App\Models\Currency;
use App\Models\Language;
use App\Models\User;
use Tests\Feature\TestCase;
use Tests\Mocks\Facebook;

class JoinTest extends TestCase
{
    use Facebook;

    private $competency;
    private $language;
    private $city;

    public function setUp()
    {
        parent::setUp();
        factory(Currency::class)->create();
        $this->city = factory(City::class)->create();
        $this->language = factory(Language::class)->create();
        $this->competency = factory(Competency::class)->create();
    }

    public function test_success_join()
    {
        $response = $this->postJson(route('api.v1.user.join', ['perspective' => 'lawyer']), $this->requestBody());
//        echo $response->baseResponse->getContent();
        $response->assertStatus(201);

        #todo avatar check?
        $profileData = $this->getProfileData();
        $this->assertDatabaseHas('users', [
            'email' => 'john@doe.com',
            'type' => User::TYPE_LAWYER,
            'first_name' => $profileData['first_name'],
            'last_name' => $profileData['last_name'],
            'phone' => $profileData['phone'],
            'latitude' => $profileData['latitude'],
            'longitude' => $profileData['longitude'],
            'device_token'=>'device_token'
        ]);
        $this->profileAssertions();
    }


    public function test_success_facebook_join()
    {
        $this->mockUser(107547836478421, 'xymbljbziq_1493217628@tfbnw.net');
        $response = $this->postJson($this->joinFacebookUrl(), $this->facebookRequestBody('EAAFtW5LecXUBAFslfNGOH9JRieTZA6RHYJz6wfpEI4sjSzUSxRyTrhVMdXvT1KMz4JuhoZBZAHOXotzFXU9WT6r9mTVEhSw3jhyNd9oDJGZAhMriwQFNn6KpdYu1eNvZATFMWaBIZCQDErzzYCIjvZBx0WFkR111LTo2Pp46nQfhUoZAfLGmZCnyqG9oQ7XRXlSrHO7ZA6Vy0iZB3kSoxjxDXKsjkuqTMp8CicZD'));

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'xymbljbziq_1493217628@tfbnw.net',
            'device_token'=>'device_token'
        ]);
        $this->profileAssertions();
    }


    private function requestBody($email = "john@doe.com", $password = 'secreT12345')
    {
        return [
            'user' => [
                'email' => $email,
                'password' => $password
            ],
            'profile' => $this->getProfileData(),
            'device_token' => 'device_token',
        ];
    }

    private function facebookRequestBody($token)
    {
        return [
            'access_token' => $token,
            'profile' => $this->getProfileData(),
            'device_token' => 'device_token',
        ];
    }

    private function getProfileData()
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Snow',
            'bar_number' => '2325235',
            'latitude' => 49.842142,
            'longitude' => 24.0003568,
            'city_id' => $this->city->id,
            'phone' => +380661384002,
            'firm' => 'Lawyer CO',
            'about' => 'About text',
            'practicing_date' => '1991-03-13',
            'linkedin' => 'http://linkedin.com',
            'website' => 'www.example.com',

            'avatar' => [
                'data' => 'R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs='
            ],
            'competencies' => [
                $this->competency->id,
            ],
            'languages' => [
                [
                    'id' => $this->language->id,
                    'level' => 4
                ]
            ]
        ];
    }

    private function joinFacebookUrl()
    {
        return route('api.v1.user.signInSocial', [
            'perspective' => 'lawyer',
            'driver' => 'facebook'
        ]);
    }

    private function profileAssertions()
    {
        $profileData = $this->getProfileData();
        $this->assertDatabaseHas('lawyer_profiles', [
            'user_id' => 1,
            'rating' => 0,
            'cases' => 0,
            'bar_number' => $profileData['bar_number'],
            'firm' => $profileData['firm'],
            'about' => $profileData['about'],
            'practicing_date' => $profileData['practicing_date'],
            'linkedin' => $profileData['linkedin'],
            'website' => $profileData['website'],
        ]);

        $this->assertDatabaseHas('lawyer_competencies', [
            'lawyer_id' => 1,
            'competency_id' => $profileData['competencies'][0]
        ]);

        $this->assertDatabaseHas('lawyer_languages', [
            'lawyer_id' => 1,
            'language_id' => $profileData['languages'][0]['id'],
            'level' => $profileData['languages'][0]['level'],
        ]);

    }

}