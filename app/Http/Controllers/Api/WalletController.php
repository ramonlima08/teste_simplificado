<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Wallet;


class WalletController extends Controller
{
    public function update(Request $request){
        try {
            $user = auth('api')->user();
            $wallet = Wallet::where('user_id', $user->id)->get()->first();
            
            if(empty($request->amount)){
                $responseArr['message'] = 'o valor é obrigatório';
                $responseArr['status'] = 0;
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }

            $amount = str_replace(',', '.', str_replace(['R$',' ','.'], '', $request->amount));

            if ($amount <= 0) {
                $responseArr['message'] = 'Valor inválido';
                $responseArr['status'] = 0;
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }

            //$newAmount = $wallet->amount + $amount;
            $wallet->update(['amount' => $amount]);
            $responseArr['message'] = 'Valor atualizado com sucesso';
            $responseArr['status'] = 1;
            $responseArr['data'] = $user->toArray();
            return response()->json($responseArr);

        } catch (\Exception $ex) {
            report($e);
            $number = 0;
            if (property_exists($e, 'errorInfo')) {
                $number = $e->errorInfo[1];
            }
            
            $responseArr = ['status'=> 0, 'message'=>"houve um erro na execução do processo", 'number'=>$number];
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
    }

    public function balance(){
        try{
            
            $user = auth('api')->user();
            return response()->json(Wallet::getBalance($user));

        } catch (\Exception $ex) {
            report($e);
            $number = 0;
            if (property_exists($e, 'errorInfo')) {
                $number = $e->errorInfo[1];
            }
            
            $responseArr = ['status'=> 0, 'message'=>"houve um erro na execução do processo", 'number'=>$number];
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
    }
}
