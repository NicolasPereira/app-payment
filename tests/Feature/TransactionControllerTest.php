<?php

namespace Tests\Feature;

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
        $response->assertJson([
            'message' => 'The given data was invalid.',
                'errors' => [
                    "payer" => [ "The selected payer is invalid."],
                    "payee" => [
                                "The selected payee is invalid.",
                                "The payee and payer must be different."
                        ],
                    ],
                ], 422);
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
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                "value" => [ "The value must be at least 0.01."],
                ],
        ], 422);
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
        $response->assertJson([
            'errors' =>
                ['message' => 'Shopkepper is not authorized to make a transactions, only receive']
        ],
            401);
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
        $response->assertJson([
            'errors' =>
                ['message' => 'The user dont have money to make the transaction']
        ],
            422);
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
        $this->assertDatabaseHas('accounts',[
            'id' => $accountPayer->id,
            'balance' => $expectedBalance
        ]);
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
        $this->assertDatabaseHas('accounts',[
            'id' => $accountPayee->id,
            'balance' => $expectedBalance
        ]);
    }

    public function testTransactionShouldNotAppearInDatabaseAfterDelete()
    {
        //Arrange
        $payer = $this->createUser();
        $payee = $this->createUser();
        $this->addCashAccount($payer->account, 100);
        $transaction = $this->createTransaction([
                    'payer_account_id' => $payer->account->id,
                    'payee_account_id' => $payee->account->id,
                    'value' => '10'
                ]);
        $this->saveEntity($transaction);
        //Act
        $response = $this->delete('api/transaction/' . $transaction->id, [],self::REQUEST_HEADERS);
        //Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('transactions',[
            'id' => $transaction->id
        ]);
    }
}
