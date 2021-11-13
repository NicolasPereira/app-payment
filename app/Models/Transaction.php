<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'payer_account_id',
        'receiver_account_id',
        'value'
    ];

    public function accountPayer() : belongsTo
    {
        return $this->belongsTo(Account::class, 'payer_account_id');
    }

    public function accountReceiver() : belongsTo
    {
        return $this->belongsTo(Account::class,'receiver_account_id');
    }
}
