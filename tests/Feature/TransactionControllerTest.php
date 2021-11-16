<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
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
            'payee' => 1,
            'value' => 10
        ];
        $response = $this->withHeaders(['Accept' => 'application/json']);
        $response = $this->post('api/transaction', $payload);
        $response->assertStatus(422);
    }

    public function test_value_is_greather_than_zero()
    {
        $payload = [
            'payer' => 1,
            'payee' => 10,
            'value' => 0.00
        ];

        $response = $this->withHeaders(['Accept' => 'application/json']);
        $response = $this->post('api/transaction', $payload);
        $response->assertStatus(422);
    }
}
