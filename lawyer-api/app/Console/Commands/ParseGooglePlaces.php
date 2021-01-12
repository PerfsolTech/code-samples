<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Country;
use App\Services\GooglePlaces;
use Illuminate\Console\Command;

class ParseGooglePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'places:parse {parseType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $googlePlaces;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GooglePlaces $googlePlaces)
    {
        parent::__construct();
        $this->googlePlaces = $googlePlaces;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('parseType')) {
            case 'country':
                $this->parseCountries();
                break;
            case 'city':
                $this->parseCities();
                break;
        }
    }

    private function parseCountries()
    {
        $countries = Country::whereNull('google_place_id')
            ->limit(1000)
            ->get();
        foreach ($countries as $country) {
            $info = $this->googlePlaces->getPlaceInfo($country->name, '(regions)');
            if (isset($info['predictions']) && !empty($info['predictions'])) {
                if (in_array('country', $info['predictions'][0]['types'])) {
                    $country->google_place_id = $info['predictions'][0]['place_id'];
                    $country->save();
                }
            }
        }
    }

    private function parseCities()
    {
        $cities = City::whereNull('google_place_id')
            ->whereIn('country_id', [212, 230, 116])
            ->orderBy('updated_at', 'ASC')
            ->limit(1000)
            ->get();


        foreach ($cities as $city) {
            $info = $this->googlePlaces->getPlaceInfo($city->name, '(cities)');
            if (isset($info['predictions']) && !empty($info['predictions'])) {
                if (in_array('political', $info['predictions'][0]['types'])) {
                    $city->google_place_id = $info['predictions'][0]['place_id'];
                    $city->save();
                } else {
                    $city->google_place_id = '*';
                    $city->save();
                }
            } else {
                $city->google_place_id = '*';
                $city->save();
            }
        }
    }
}
