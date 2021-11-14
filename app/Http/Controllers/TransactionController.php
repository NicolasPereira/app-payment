<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientCashException;
use App\Exceptions\PayeeAndPayerIsSameException;
use App\Exceptions\PayeeExistsException;
use App\Exceptions\PayerExistsException;
use App\Exceptions\ShopkepperMakeTransactionException;
use App\Http\Requests\TransactionPostRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
