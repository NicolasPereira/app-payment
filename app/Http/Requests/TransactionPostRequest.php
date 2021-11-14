<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payer' => ['required', 'exists:users,id'],
            'payee' => ['required', 'exists:users,id', 'different:payer_id'],
            'value' => ['required', 'numeric'],
        ];
    }
}
