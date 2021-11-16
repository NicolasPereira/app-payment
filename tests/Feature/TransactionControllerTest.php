<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const REQUEST_HEADERS = [
        'accept' => 'application/json'
    ];
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
        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function test_value_is_greather_than_zero()
    {
        $payload = [
            'payer' => 1,
            'payee' => 10,
            'value' => 0.00
        ];

        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function test_payer_is_shopkeeper()
    {
        $payer = User::factory()->create(['profile' => 'shopkeeper']);
        $payee = User::factory()->create();
        $accountPayer = Account::factory()->create(['user_id' => $payer->id]);
        $accountPayee = Account::factory()->create(['user_id' => $payee->id]);
        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 0.01
        ];

        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(401);
    }

    public function test_account_payer_balance()
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();
        $accountPayer = Account::factory()->create(['user_id' => $payer->id]);
        $accountPayee = Account::factory()->create(['user_id' => $payee->id]);

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 300.01
        ];

        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }
}
