<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\GeoSeeder::class);
        $this->call(\CompetencySeeder::class);
        $this->call(\DataSeeder::class);
        $this->call(\PermissionsSeeder::class);
        $this->call(\MenuSeeder::class);
        $this->call(\PagesSeeder::class);
    }
}
