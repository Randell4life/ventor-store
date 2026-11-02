<?php

namespace Tests\Feature;

use Tests\TestCase;

class StoreSmokeTest extends TestCase
{
    public function test_homepage_is_available(): void
    {
        $this->get('/')->assertOk()->assertSee('VENTOR');
    }
}
