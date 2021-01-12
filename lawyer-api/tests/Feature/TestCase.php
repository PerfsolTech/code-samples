<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends \Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();

        DB::beginTransaction();
        Artisan::call('migrate:refresh');
    }

    public function tearDown()
    {
        DB::rollBack();
        parent::tearDown();
    }
}