<?php

namespace App\Repositories;

use App\Exceptions\AuthorizeServiceUnavailableException;
use App\Exceptions\InsufficientCashException;
use App\Exceptions\PayeeAndPayerIsSameException;
use App\Exceptions\PayerExistsException;
use App\Exceptions\PayeeExistsException;
use App\Exceptions\ShopkepperMakeTransactionException;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuthorizeTransactionService;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TransactionRepository
{
    protected $serviceAuthorizeTransaction;
    public function __construct(AuthorizeTransactionService $serviceAuthorizeTransaction)
    {
        $this->serviceAuthorizeTransaction = $serviceAuthorizeTransaction;
    }
    public function index(array $data)
    {
        if($data['payee_id'] === $data['payer_id']){
            throw new PayeeAndPayerIsSameException('Payee and Payeer is same ID', 422);
        }
        if($this->verifyPayerIsShopkepper($data['payer_id'])){
            throw new ShopkepperMakeTransactionException('Shopkepper is not authorized to make a transactions, only receive', 401);
        }

        if(!$this->verifyPayerExists($data['payer_id'])){
            throw new PayerExistsException('Payer not found', 404);
        }

        if(!$this->verifyPayeeExists($data['payee_id'])){
            throw new PayeeExistsException('Receveier not found', 404);
        }
        $payer = User::find($data['payer_id']);
        $payee = User::find($data['payee_id']);
        $payerAccount = $payer->account;
        if (!$this->checkAccountPayerBalance($payerAccount, $data['value'])) {
            throw new InsufficientCashException('The user dont have money to make the transaction', 422);
        }

        if (!$this->verifyAuthorizeTransaction()){
            throw new AuthorizeServiceUnavailableException('Service is unavailable! Try again in few minutes.', 503);
        }

        $transaction = $this->makeTransaction($payer, $payee, $data);

        return $transaction;
    }

    public function makeTransaction($payer, $payee, $data): Transaction
    {
        $payload = [
            'id' => Uuid::uuid4()->toString(),
            'payer_account_id' => $payer->account->id,
            'payee_account_id' => $payee->account->id,
            'value' => $data['value']
        ];
        return DB::transaction(function () use($payload){
            $transaction = Transaction::create($payload);
            $transaction->accountPayer->removeCash($payload['value']);
            $transaction->accountPayee->addCash($payload['value']);
            return $transaction;
        });
    }

    public function verifyPayerIsShopkepper(string $payer_id):bool
    {
        try {
            $payer = User::find($payer_id);
            return $payer->IsShopkeeper();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyPayeeExists(string $payee_id):bool
    {
        try {
            $payee = User::find($payee_id);
            return (bool)$payee;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function verifyPayerExists(string $payer_id):bool
    {
        try {
            $payer = User::find($payer_id);
            return (bool)$payer;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function checkAccountPayerBalance(Account $account, $value)
    {
        return $account->balance >= $value;
    }

    public function verifyAuthorizeTransaction():bool
    {
       $response = $this->serviceAuthorizeTransaction->verifyAuthorizeTransaction();
       return $response['message'] === 'Autorizado';
    }
}
