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

    public function testUserShouldNotSendRequestAcceptHeadersWrong()
    {
        $response = $this->withHeaders(['Accept' => '*/*'])->post('api/transaction');
        $response->assertStatus(406);
        $response->assertJson([
            'errors' =>
                ['message' => 'O método de comunicação deve ser application/json']
        ],
            406);
    }

    public function testPayerShouldNotSamePayee()
    {
        $payload = [
            'payer' => 1,
            'payee' => 1,
            'value' => 10
        ];
        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function testValueShouldIsGreaterThanZero()
    {
        $payload = [
            'payer' => 1,
            'payee' => 10,
            'value' => 0.00
        ];

        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function testUserShouldNotIsShopkeeper()
    {
        $payer = $this->createUser(['profile' => 'shopkeeper']);
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $this->addCashAccount($accountPayer, 10);
        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 0.01
        ];

        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(401);
    }

    public function testUserShouldNotMakeTransactionIfWithoutMoney()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $this->addCashAccount($accountPayer, 10);

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 300.01
        ];
        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(422);
    }

    public function testUserAccountShouldCorrectAfterSendTransaction()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $this->addCashAccount($accountPayer, 100);

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];
        $expectedBalance = $accountPayer->balance - $payload['value'];
        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(201);
        $accountPayer = Account::find($accountPayer->id);

        $this->assertTrue($accountPayer->balance == $expectedBalance, 'O saldo da conta {$accountPayer->id} está incorreto, o correto eh: {$accountPayer->balance} != $expectedBalance.');
    }

    public function testUserAccountShouldCorrectAfterReceiveTransaction()
    {
        $payer = $this->createUser();
        $payee = $this->createUser();
        $accountPayer = $payer->account;
        $accountPayee = $payee->account;
        $this->addCashAccount($accountPayer, 100);

        $payload = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];
        $expectedBalance = $accountPayee->balance + $payload['value'];
        $response = $this->post(route('transaction'), $payload, self::REQUEST_HEADERS);
        $response->assertStatus(201);
        $accountPayee = Account::find($accountPayee->id);

        $this->assertTrue($accountPayee->balance == $expectedBalance, 'O saldo da conta {$accountPayee->id} está incorreto, o correto eh: {$accountPayee->balance} != $expectedBalance.');
    }
}
