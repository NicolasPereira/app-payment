<?php

namespace Tests;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUser(array $options = [])
    {
        return User::factory()->create($options);
    }

    protected  function createTransaction(array $options = [])
    {
        return Transaction::factory()->create($options);
    }

    protected function addCashAccount(Account $account, $value):void
    {
        $account->balance = $value;
        $account->save();
    }

    protected function saveEntity($model):void
    {
        $model->save();
    }
}
