<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NfeEndpointTest extends TestCase
{
    /**
     * Testa o get por access key
     *
     * @return void
     */
    public function testNfeEndpoint()
    {
        $response = $this->get('/api/nfe/35110883932854000133550010000001141840787155');
        $response->assertStatus(200);

        $response = $this->get('/api/nfe');
        $response->assertStatus(400);

        $response = $this->get('/api/nfe/123123123121312354123');
        $response->assertStatus(200);
    }
}
