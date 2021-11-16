<?php

namespace App\Models;

use App\Traits\hasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;
    use hasUuid;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'balance'
    ];

    protected $attributes = [
        "balance" => 0.0
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
