<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'amount'
    ];

    static function checkBalance($user, $transactionAmount){
        $balance = Wallet::where('user_id', $user->id)->get()->first();

        if ($balance->amount < $transactionAmount) {
            return false;
        }

        return true;
    }

    static function getBalance($user){
        return Wallet::where('user_id', $user->id)->get()->first();
    }

    static function updateBalanceFrom($user_id, $transactionAmount){
        //de (menos)
        $balance = Wallet::where('user_id', $user_id)->get()->first();
        $newBalance = $balance->amount - $transactionAmount;
        $balance->update(['amount'=>$newBalance]);
    }

    static function updateBalanceTo($user_id, $transactionAmount){
        //para (mais)
        $balance = Wallet::where('user_id', $user_id)->get()->first();
        $newBalance = $balance->amount + $transactionAmount;
        $balance->update(['amount'=>$newBalance]);
    }

}
