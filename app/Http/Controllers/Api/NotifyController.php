<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Notification;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Http;

class NotifyController extends Controller
{
    public function send(Request $request){

        try{
            $flagOk = true;
            if(!isset($request->transaction_id)){
                return response()->json(['status'=>0, 'message'=>'O campo transaction é obrigatório'], Response::HTTP_BAD_REQUEST);
            }
    
            $transaction = TransactionHistory::find($request->transaction_id);

            //COM A TRANSAÇÃO AQUI, PODEMOS ESCOLHER QUAL LAYOUT DE EMAIL
            //OU TEXTO DE SMS PADRÃO PARA ENVIAR EX (Msg de envio, Msg de estorno)

            //URL para enviar Notificacao
            $response = Http::get('http://o4d9z.mocklab.io/notify');
            $jsonReturn = $response->json();
            //return response()->json($jsonReturn);
            if($jsonReturn['message'] != "Success"){
                $flagOk = false;
            }
            
            $notificacaoFrom = [
                'transaction_id' => $transaction->id,
                'user_id' =>$transaction->user_id_from,
                'date' => date('Y-m-d H:i:s')
            ];

            $notificacaoTo = [
                'transaction_id' => $transaction->id,
                'user_id' =>$transaction->user_id_to,
                'date' => date('Y-m-d H:i:s')
            ];

            if($flagOk){
                Notification::create($notificacaoFrom);
                Notification::create($notificacaoTo);
            }
            
            $response = ['status'=>1, 'message'=>'Notificação enviada com sucesso'];
            return response()->json($response);

        } catch (\Exception $e) {
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
