<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientCashException;
use App\Exceptions\PayeeAndPayerIsSameException;
use App\Exceptions\PayerExistsException;
use App\Exceptions\ShopkepperMakeTransactionException;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;

class ServiceTransactionValidate
{
    protected $accountRepository;
    protected $userRepository;

    public function __construct( AccountRepository $accountRepository, UserRepository $userRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
    }

    public function validatePayerAndPayeeIsSame($payee, $payer)
    {
        if($payee === $payer){
            throw new PayeeAndPayerIsSameException('Payee and Payeer is same ID', 422);
        }
    }

    public function validateUserExists($user)
    {
        if(!$this->userRepository->verifyUserExists($user)){
            throw new PayerExistsException('Payer not found', 404);
        }
    }

    public function validateAccountExists($user_id_account)
    {
        if(!$this->accountRepository->checkAccountExists($user_id_account)){
            throw new AccountNotFoundException('Account not found', 404);
        }
    }

    public function validatePayerIsShopkepper($user){
        if($this->userRepository->isShopkeeper($user)){
            throw new ShopkepperMakeTransactionException('Shopkepper is not authorized to make a transactions, only receive', 401);
        }
    }

    public function validateCheckBalance($account, $value){
        if(!$this->accountRepository->checkAccountBalance($account, $value)){
            throw new InsufficientCashException('The user dont have money to make the transaction', 422);
        }
    }
}
