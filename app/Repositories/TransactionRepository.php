<?php

namespace App\Repositories;

use App\Exceptions\AuthorizeServiceUnavailableException;
use App\Exceptions\InsufficientCashException;
use App\Exceptions\PayerExistsException;
use App\Exceptions\ReceiverExistsException;
use App\Exceptions\ShopkepperMakeTransactionException;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuthorizeTransactionService;

class TransactionRepository
{
    private $serviceAuthorizeTransaction;
    public function __construction(AuthorizeTransactionService $serviceAuthorizeTransaction)
    {
        $this->serviceAuthorizeTransaction = $serviceAuthorizeTransaction;
    }
    public function index(array $data)
    {
        if(!$this->verifyPayerIsShopkepper($data['payer_id'])){
            throw new ShopkepperMakeTransactionException('Shopkepper is not authorized to make a transactions, only receive', 401);
        }

        if(!$this->verifyPayerExists($data['payer_id'])){
            throw new PayerExistsException('Payer not found', 404);
        }

        if(!$this->verifyReceiverExists($data['receiver_id'])){
            throw new ReceiverExistsException('Receveier not found', 404);
        }
        $payerUser = User::find($data['payer_id']);
        $payerAccount = $payerUser->account;
        if (!$this->checkAccountPayerBalance($payerAccount, $data['value'])) {
            throw new InsufficientCashException('The user dont have money to make the transaction', 422);
        }

        if (!$this->verifyAuthorizeTransaction()){
            throw new AuthorizeServiceUnavailableException('Service is unavailable! Try again in few minutes.', 503);
        }
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

    public function verifyReceiverExists(string $receiver_id):bool
    {
        try {
            $receiver = User::find($receiver_id);
            return ($receiver);
        }catch(\Exception $e) {
            return false;
        }
    }

    public function verifyPayerExists(string $payer_id):bool
    {
        try {
            $payer = User::find($payer_id);
            return ($payer);
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
       return $response['messsage'] == 'Autorizado';
    }
}
