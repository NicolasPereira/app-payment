<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function __construct()
    {

    }

    public function addCash(Account $account, $value): void
    {
        $account->update([
            'balance' => $account->balance + $value,
        ]);
    }

    public function removeCash(Account $account, $value): void
    {
        $account->update([
            'balance' => $account->balance - $value,
        ]);
    }

    public function checkAccountBalance(Account $account, $value): bool
    {
        return $account->balance >= $value;
    }
}
