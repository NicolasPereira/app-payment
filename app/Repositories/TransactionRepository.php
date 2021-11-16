<?php

namespace App\Repositories;

use App\Exceptions\AuthorizeServiceUnavailableException;
use App\Models\Transaction;
use App\Services\AuthorizeTransactionService;
use App\Services\NotificationService;
use App\Services\ServiceTransactionValidate;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    protected $serviceAuthorizeTransaction;
    protected $serviceNotification;
    protected $accountRepository;
    protected $userRepository;
    protected $validateService;
    public function __construct(AuthorizeTransactionService $serviceAuthorizeTransaction, NotificationService $serviceNotification, AccountRepository $accountRepository, UserRepository $userRepository, ServiceTransactionValidate $validateService)
    {
        $this->serviceAuthorizeTransaction = $serviceAuthorizeTransaction;
        $this->serviceNotification = $serviceNotification;
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
        $this->validateService = $validateService;
    }
    public function index(array $data): Transaction
    {
        $this->validateService->validateExecute($data);

        $payer = $this->userRepository->find($data['payer_id']);
        $payee = $this->userRepository->find($data['payee_id']);

        if (!$this->verifyAuthorizeTransaction()){
            throw new AuthorizeServiceUnavailableException('Service is unavailable! Try again in few minutes.', 503);
        }

        $transaction = $this->makeTransaction($payer, $payee, $data['value']);

        $this->sendNotification();

        return $transaction;
    }

    public function makeTransaction($payer, $payee, $value): Transaction
    {
        $payload = [
            'payer_account_id' => $payer->account->id,
            'payee_account_id' => $payee->account->id,
            'value' => $value
        ];
        return DB::transaction(function () use($payer, $payee, $payload){
            $transaction = Transaction::create($payload);
            $this->accountRepository->removeCash($payer->account, $payload['value']);
            $this->accountRepository->addCash($payee->account, $payload['value']);
            return $transaction;
        });
    }

    public function verifyAuthorizeTransaction():bool
    {
       $response = $this->serviceAuthorizeTransaction->verifyAuthorizeTransaction();
       return $response['message'] === 'Autorizado';
    }

    public function sendNotification():bool
    {
        $response = $this->serviceNotification->sendNotification();
        return $response['message'] === 'Success';
    }
}
