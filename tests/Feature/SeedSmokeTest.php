<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeedSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_runs_without_memory()
    {
        $this->seed();
        $this->assertTrue(true);
    }
}
