<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $fillable = [
        'transaction_type', 'user_id_from', 'user_id_to','date','amount','status','is_rollback', 'rollback_transaction_id'
    ];
}
