<?php

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'phone' => $faker->randomNumber(9),
        'avatar' => 'https://blogs-images.forbes.com/ericsavitz/files/2011/03/smiley-face.jpg',
        'gender' => $faker->boolean ? 'MALE' : 'FEMALE',
        'first_name' => $faker->firstName,
        'last_name' => $faker->firstName,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('secret'),
        'remember_token' => str_random(10),
        'registration_ip' => ip2long($faker->ipv4),
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'language_id' => factory(App\Models\Language::class)->create()->id,
        'currency_id' => 1,
        'status' => \App\Models\User::STATUS_ACTIVE,
        'type' => \App\Models\User::TYPE_LAWYER,
        'settings' => (new \App\Services\UserService\Settings())->getDefaultSettings(\App\Models\User::TYPE_LAWYER)
    ];
});

$factory->define(App\Models\LawyerProfile::class, function (Faker\Generator $faker) {
    return [
        'birthday' => $faker->date('Y-m-m', '-20 years'),
        'practicing_date' => $faker->date(),
        'website' => $faker->url,
        'firm' => $faker->company,
        'rating' => $faker->randomFloat(2, 0, 5),
        'address' => $faker->address,
        'cases' => $faker->randomDigitNotNull,
    ];
});


$factory->defineAs(App\Models\LawyerProfile::class, 'john', function (Faker\Generator $faker) {
    return [
        'birthday' => $faker->date('Y-m-m', '-20 years'),
        'practicing_date' => $faker->date(),
        'website' => $faker->url,
        'firm' => $faker->company,
        'rating' => $faker->randomFloat(1, 0, 5),
        'address' => $faker->address,
        'cases' => $faker->randomDigitNotNull,
    ];
});


$factory->define(\App\Models\LawyerCompetency::class, function (Faker\Generator $faker) {
    return [
        'competency_id' => factory(App\Models\Competency::class)->create()->id,
    ];
});

$factory->define(\App\Models\Competency::class, function (Faker\Generator $faker) {
    return [
        'name' => 'family',
    ];
});

$factory->define(\App\Models\LawyerLanguage::class, function (Faker\Generator $faker) {
    return [
        'language_id' => factory(App\Models\Language::class)->create()->id,
        'level' => rand(1, 5),
    ];
});


$factory->define(\App\Models\City::class, function (Faker\Generator $faker) {
    return [
        'country_id' => factory(\App\Models\Country::class)->create()->id,
        'name' => 'Beirut',
        'google_place_id' => 'ChIJj6eAWCEXHxURtDaY6bqCkXI',
        'latitude' => 33.8914506,
        'longitude' => 35.4805142,
        'status' => 'ENABLED'
    ];
});

$factory->define(\App\Models\Country::class, function (Faker\Generator $faker) {
    return [
        'code' => 'UA',
        'name' => 'Ukraine',
    ];
});

$factory->define(\App\Models\Language::class, function (Faker\Generator $faker) {
    return [
        'code' => 'en',
        'name' => 'English'
    ];
});


$factory->define(\App\Models\UserActivation::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\Models\User::class)->create()->id,
        'token' => $faker->randomNumber(10)
    ];
});

$factory->define(\App\Models\UserActivation::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\Models\User::class)->create()->id,
        'token' => $faker->randomNumber(10)
    ];
});

$factory->define(\App\Models\PasswordReset::class, function (Faker\Generator $faker) {
    return [
        'email' => factory(App\Models\User::class)->create()->email,
        'token' => password_hash('123', PASSWORD_BCRYPT, ['cost' => '10'])
    ];
});

$factory->define(\App\Models\UserPassword::class, function (Faker\Generator $faker) {
    return [
    ];
});


$factory->define(\App\Models\CaseModel::class, function (Faker\Generator $faker) {
    $statuses = [
        'OPEN',
        'PENDING',
        'CLOSED'
    ];
    return [
        'number' => 124124214,
        'competency_id' => factory(App\Models\Competency::class)->create()->id,
        'user_id' => factory(App\Models\User::class)->create()->id,
        'language_id' => factory(App\Models\Language::class)->create()->id,
        'city_id' => factory(App\Models\City::class)->create()->id,
        'title' => $faker->text('100'),
        'message' => $faker->text('500'),
        'status' => $statuses[rand(0, 2)],
    ];
});

$factory->define(\App\Models\Review::class, function (Faker\Generator $faker) {
    $statuses = [
        'MODERATION',
        'APPROVED',
        'DECLINED'
    ];
    return [
        'reviewer_id' => factory(\App\Models\User::class)->create(),
        'title' => $faker->text('100'),
        'body' => $faker->text('500'),
        'rating' => random_int(1, 5),
        'status' => $statuses[rand(0, 2)],
    ];
});

$factory->define(\App\Models\Currency::class, function (Faker\Generator $faker) {
    return [
        'code' => 'USD',
        'symbol' => '$'
    ];
});

$factory->define(App\Models\Attachment::class, function (Faker\Generator $faker) {
    return [
        'name' => 'fafaffaf',
    ];
});

$factory->define(App\Models\CaseAttachment::class, function (Faker\Generator $faker) {
    return [

    ];
});

$factory->define(App\Models\Page::class, function (Faker\Generator $faker) {
    return [
        'body' => $faker->text('1000'),
        'title' => $faker->text(50)
    ];
});


$factory->define(App\Models\Faq::class, function (Faker\Generator $faker) {
    return [
        'question' => $faker->text('100'),
        'answer' => $faker->text('100'),
        'answer_text' => $faker->text('100'),
    ];
});


