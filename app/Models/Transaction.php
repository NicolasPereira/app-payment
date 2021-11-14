<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'payer_account_id',
        'payee_account_id',
        'value'
    ];

    public function accountPayer() : belongsTo
    {
        return $this->belongsTo(Account::class, 'payer_account_id');
    }

    public function accountPayee() : belongsTo
    {
        return $this->belongsTo(Account::class,'payee_account_id');
    }
}
