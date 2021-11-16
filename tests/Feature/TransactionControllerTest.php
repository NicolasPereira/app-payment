<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_request_accept_headers()
    {
        $response = $this->withHeaders(['Accept' => '*/*'])->post('api/transaction');
        $response->assertStatus(406);
    }
}
