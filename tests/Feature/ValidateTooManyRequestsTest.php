<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ValidateTooManyRequests extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        for ($i = 0; $i < 150; $i++) {

            $nfe = DB::select('select access_key from nfes order by random() limit 1;')[0];
            
            $response = $this->get('/api/nfe/'.$nfe->access_key);
            // aqui eu testo apenas 97 requisiçoes porque o outro teste (NfeEndpointTest) já gasta 3 requisiçoes
            if ($i < 97) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429);
            }
        }
    }
}
