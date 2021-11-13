<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addCash($value): void
    {
        $this->update([
            'balance' => $this->attributes['balance'] + $value
        ]);
    }

    public function removeCash($value): void
    {
        $this->update([
            'balance' => $this->attributes['balance'] - $value
        ]);
    }
}