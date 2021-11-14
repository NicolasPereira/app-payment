<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionPostRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
class TransactionController extends Controller
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function create(TransactionPostRequest $request)
    {
        $payload = [
                'payer_id' => $request->payer,
                'payee_id' => $request->payee,
                'value' => $request->value
        ];
        try{
            $transaction = $this->transactionRepository->index($payload);
            return new TransactionResource($transaction);
        }catch(\Exception $exception){
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        }


    }
}
