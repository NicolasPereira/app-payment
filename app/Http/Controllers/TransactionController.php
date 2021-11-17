<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionPostRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;

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

    public function index()
    {
        return new TransactionCollection($this->transactionRepository->listAllTransaction());
    }
    public function create(TransactionPostRequest $request)
    {
        $payload = [
                'payer_id' => $request->payer,
                'payee_id' => $request->payee,
                'value' => $request->value
        ];
        try{
            $transaction = $this->transactionRepository->creteTransaction($payload);
            return new TransactionResource($transaction);
        }catch(\Exception $exception){
            return response()->json(['errors' => ['message' => $exception->getMessage()]], $exception->getCode());
        }
    }

    public function show(Transaction $transaction): TransactionResource
    {
        try{
                $response = new TransactionResource($transaction);
        }catch (\Exception $exception){
            return response()->json(["message" => $exception->getMessage()], 500);
        }

        return $response;
    }

    public function delete(Transaction $transaction): JsonResponse
    {
        try {
            $transaction->delete();
            return response()->json(["message" => "Transação excluída com sucesso."], 204);
        } catch(\Exception $exception){
            return response()->json(["message" => "Ocorreu uma falha inesperada ao tentar excluir a transação."], 500);
        }
    }
}
