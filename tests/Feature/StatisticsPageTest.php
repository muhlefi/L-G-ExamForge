<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_page_returns_successful_response(): void
    {
        $response = $this->get(route('stats.index'));

        $response->assertOk();
        $response->assertSee('Statistik Penggunaan');
    }
}
