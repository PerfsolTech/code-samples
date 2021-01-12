<?php

use App\Models\Competency;

class CompetencySeeder extends \Illuminate\Database\Seeder
{

    public function run()
    {
        Competency::insert($this->getCompetencyData());

    }

    private function getCompetencyData()
    {
        return [
            [
                'name' => 'Admiralty & Maritime'
            ],
            [
                'name' => 'Antitrust & Trade Regulation'
            ],
            [
                'name' => 'Aviation Law'
            ],
            [
                'name' => 'Banking & Finance'
            ],
            [
                'name' => 'Bankruptcy'
            ],
            [
                'name' => 'Business & Industry'
            ],
            [
                'name' => 'Civil Rights'
            ],
            [
                'name' => 'Communication'
            ],
            [
                'name' => 'Criminal Law'
            ],
            [
                'name' => 'Divorce'
            ],
            [
                'name' => 'Education Law'
            ],
            [
                'name' => 'Employment'
            ],

            [
                'name' => 'Environmental'
            ],

            [
                'name' => 'Estate Planning'
            ],

            [
                'name' => 'Ethics'
            ],

            [
                'name' => 'Family Law'
            ],

            [
                'name' => 'General Practice'
            ],

            [
                'name' => 'Government'
            ],

            [
                'name' => 'Health Care & Social'
            ],

            [
                'name' => 'Immigration'
            ],

            [
                'name' => 'Insurance'
            ],

            [
                'name' => 'Intellectual Property'
            ],

            [
                'name' => 'International Law'
            ],

            [
                'name' => 'Leisure'
            ],

            [
                'name' => 'Litigation'
            ],

            [
                'name' => 'Motor Vehicles'
            ],

            [
                'name' => 'Nonprofit Organizations'
            ],

            [
                'name' => 'Personal Injury'
            ],

            [
                'name' => 'Real Estate'
            ],

            [
                'name' => 'Tax'
            ],
        ];
    }

}