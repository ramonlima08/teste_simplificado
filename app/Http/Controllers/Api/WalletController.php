<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Wallet;


class WalletController extends Controller
{
    public function update(Request $request){
        $user = auth('api')->user();
        $wallet = Wallet::where('user_id', $user->id)->get()->first();
        $amount = str_replace(',','.',str_replace(['R$',' ','.'], '', $request->amount));

        if($amount <= 0){
            $responseArr['message'] = 'Valor inválido';
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        //$newAmount = $wallet->amount + $amount;
        $wallet->update(['amount' => $amount]);
        $responseArr['message'] = 'Valor atualizado com sucesso';
        $responseArr['status'] = 0;
        $responseArr['data'] = $user->toArray();
        return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
    }

    public function balance(){
        $user = auth('api')->user();
        return response()->json(Wallet::getBalance($user));
    }
}
