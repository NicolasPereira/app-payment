<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientCashException;
use App\Exceptions\PayeeAndPayerIsSameException;
use App\Exceptions\PayerExistsException;
use App\Exceptions\ShopkepperMakeTransactionException;
use App\Models\Account;
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

    public function validateExecute(array $data)
    {
        $this->validatePayerIsShopkepper($data['payer_id']);
        $this->validateCheckBalance($data['payer_id'], $data['value']);
    }

    private function validatePayerIsShopkepper($user){
        if($this->userRepository->isShopkeeper($user)){
            throw new ShopkepperMakeTransactionException('Shopkepper is not authorized to make a transactions, only receive', 401);
        }
    }

    private function validateCheckBalance($user, $value){
        $account  = Account::where('user_id', $user)->first();
        if(!$this->accountRepository->checkAccountBalance($account, $value)){
            throw new InsufficientCashException('The user dont have money to make the transaction', 422);
        }
    }
}
