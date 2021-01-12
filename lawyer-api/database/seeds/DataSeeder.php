<?php

use App\Models\Language;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    const BEIRUT_ID = 1416705;
    const LVIV_ID = 2497186;

    private $lawyerCoordinates = [
        //beirut
        [33.8914506, 35.4805142],
        [33.8887075, 35.4997832],
        [33.8620024, 35.5099756],

        [33.875454, 35.4924448],
        [33.887853, 35.4816733],
        [33.888761, 35.4836693],
        [33.8921504, 35.4703474],
        [33.8939487, 35.4948293],
        [33.8944296, 35.5019532],
        [33.8942996, 35.5059547],
        [33.8913244, 35.5002845],
        [33.8941153, 35.4936989],
        [33.8965959, 35.4899959],
        [33.8965959, 35.4899959],
        [33.8965959, 35.4899959],
        [33.8965959, 35.4899959],
        [33.8923049, 35.4893815],
        [33.8917156, 35.4821634],
        [33.8862828, 35.4823243],
        [33.8830141, 35.4794919],
        [33.8830141, 35.4794919],
        [33.8860869, 35.4729688],
        [33.8898454, 35.4707801],

        //lviv
        [49.869636, 23.927776],
        [49.800872, 24.072315],
        [49.829899, 24.035924],
        [49.862299, 24.040709],
    ];

    const AVATARS = [
        'william-howard-taft-1.jpg',
        'trey-gowdy-1.jpg',
        'kimberly-guilfoyle-1.jpg',
        'john-marshall-1.jpg',
        'ben-stein-1.jpg',
        'dembitz-brandeis-1.jpg'
    ];

    private $faker;

    /**
     * DataSeeder constructor.
     * @param $faker
     */
    public function __construct(\Faker\Generator $faker)
    {
        $this->faker = $faker;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedCurrencies();
        $user = $this->seedTestUser();


        if (app()->environment() === 'production') {
            return;
        }
        for ($i = 1; $i <= 2; $i++) {
            $this->seedCase($user->id);
        }

        $this->seedLawyers();
    }


    private function seedCurrencies()
    {
        \App\Models\Currency::firstOrCreate([
            'id' => 1,
            'code' => 'USD',
            'symbol' => '$'
        ]);
    }

    private function seedLawyers()
    {
        for ($i = 0; $i < 40; $i++) {
            $lat = isset($this->lawyerCoordinates[$i]) ? $this->lawyerCoordinates[$i][0] : null;
            $long = isset($this->lawyerCoordinates[$i]) ? $this->lawyerCoordinates[$i][1] : null;
            $user = $this->seedUser($lat, $long);
            $this->seedLawyerProfile($user->id);
        }
    }

    private function seedUser($lat = null, $long = null)
    {
        $user = new \App\Models\User([
            'gender' => $this->faker->boolean ? 'MALE' : 'FEMALE',
            'avatar' => self::AVATARS[rand(0, 5)],
            'phone' => '+380' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('secret'),
            'remember_token' => str_random(10),
            'registration_ip' => ip2long($this->faker->ipv4),

            'latitude' => $lat ?? $this->faker->latitude,
            'longitude' => $long ?? $this->faker->longitude,
            'language_id' => rand(1, 10),
            'currency_id' => 1,
            'status' => App\Models\User::STATUS_ACTIVE,
            'type' => \App\Models\User::TYPE_LAWYER,
            'settings' => (new \App\Services\UserService\Settings())->getDefaultSettings(\App\Models\User::TYPE_LAWYER)
        ]);

        $user->save();

        $user->competencies()
            ->create(['competency_id' => rand(1, 2)]);

        $user->languages()->create([
            'language_id' => rand(1, 3),
            'level' => rand(1, 5)
        ]);
        $user->languages()->create([
            'language_id' => rand(4, 7),
            'level' => rand(1, 5)
        ]);
        $user->languages()->create([
            'language_id' => rand(8, 10),
            'level' => rand(1, 5)
        ]);
        $user->reviews()->create([
            'reviewer_id' => \App\Models\User::first()->id,
            'title' => $this->faker->text(50),
            'body' => $this->faker->text(500),
            'rating' => rand(1, 5),
            'status' => 'APPROVED',
        ]);

        return $user;
    }

    private function seedLawyerProfile($user_id)
    {
        $lawyer = new \App\Models\LawyerProfile([
            'user_id' => $user_id,
            'birthday' => $this->faker->date('Y-m-m', '-20 years'),
            'practicing_date' => $this->faker->date(),
            'website' => $this->faker->url,
            'firm' => $this->faker->company,
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'address' => $this->faker->address,
            'cases' => $this->faker->randomDigitNotNull,
            'about' => $this->faker->text(500),
        ]);

        $lawyer->save();
    }

    private function seedTestUser()
    {
        $user = new \App\Models\User([
            'first_name' => 'Mykola',
            'last_name' => 'Breslavskyi',
            'email' => 'kolyabres@gmail.com',
            'password' => bcrypt('secret'),
            'remember_token' => str_random(10),
            'registration_ip' => ip2long($this->faker->ipv4),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'language_id' => 1,
            'currency_id' => 1,
            'status' => \App\Models\User::STATUS_ACTIVE,
            'type' => \App\Models\User::TYPE_CLIENT,
            'settings' => (new \App\Services\UserService\Settings())->getDefaultSettings(\App\Models\User::TYPE_CLIENT)
        ]);
        $user->save();
        $user->token()->create([
            'access_token' => 'secret',
            'refresh_token' => 'secret',
            'firebase_token' => 'secret',
            'expires_at' => date("Y-m-d H:i:s", time() + 24 * 60 * 60 * 30)
        ]);
        $user->activation()->create([
            'token' => 'sdfsdfsdfdsf'
        ]);

        $user->social()->create([
            'identity' => 132123123,
            'type' => 'facebook',
            'activation_token' => 'f2f2f2f2f22f'
        ]);
        return $user;
    }

    private function seedCase($user_id)
    {
        $case = new \App\Models\CaseModel([
            'number' => 123456,
            'title' => 'test case',
            'message' => 'help me',
            'user_id' => $user_id,
            'city_id' => self::LVIV_ID,
            'language_id' => 1,
            'competency_id' => \App\Models\Competency::first()->id
        ]);
        $case->save();
        return $case;
    }


}
