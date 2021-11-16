<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const REQUEST_HEADERS = [
        'accept' => 'application/json'
    ];

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
        $payer = $this->createUser(['profile' => 'shopkeeper']);
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $accountPayee = $payee->account;
        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 0.01
        ];

        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(401);
    }

    public function test_account_payer_balance_can_have_money_make_transaction()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $this->addCashAccount($accountPayer, 1000);
        $accountPayer->save();

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 300.01
        ];

        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function test_balance_payer_is_ok_after_transaction()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayer = $payer->account;

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];
        $expectedBalance = $accountPayer->balance - $payload['value'];
        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(201);
        $accountPayer = Account::find($accountPayer->id);

        $this->assertTrue($accountPayer->balance == $expectedBalance, 'O saldo da conta {$accountPayer->id} está incorreto, o correto eh: {$accountPayer->balance} != $expectedBalance.');
    }

    public function test_balance_payee_is_ok_after_transaction()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayee = $payee->account;

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];
        $expectedBalance = $accountPayee->balance + $payload['value'];
        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(201);
        $accountPayee = Account::find($accountPayee->id);

        $this->assertTrue($accountPayee->balance == $expectedBalance, 'O saldo da conta {$accountPayee->id} está incorreto, o correto eh: {$accountPayee->balance} != $expectedBalance.');
    }

    public function test_user_dont_have_account()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];
        $response = $this->post('api/transaction', $payload, self::REQUEST_HEADERS);
        $response->assertStatus(404);
    }
}
