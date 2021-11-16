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

    public function test_payer_and_payee_equals()
    {
        $payload = [
            'payer' => 1,
            'payer' => 1,
            'value' => 10
        ];
        $response = $this->withHeaders(['Accept' => 'application/json']);
        $response = $this->post(route('postTransaction'), $payload);
        $response->assertStatus(422);
    }
}
