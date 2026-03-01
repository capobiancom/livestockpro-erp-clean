<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_name()
    {
        $this->seed();
        echo route('breeds.store');
        $this->assertTrue(true);
    }
}
