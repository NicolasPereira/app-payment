<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    /**
     * The "transaction" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'transaction';

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'created_at' => $this->created_at,
            'payer' => new PayerAccountTransactionResource($this->accountPayer),
            'payee' => new UserTransactionResource($this->accountPayee->users),
        ];
    }
}
