<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TransactionHistory;
use App\Models\Wallet;
use App\User;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    private $error=false;
    private $errorMessage="";
    
    public function history(){
        
        $transactions = TransactionHistory::where(function ($query) {
            $user = auth('api')->user();
            $query->where('user_id_to', '=', $user->id)
            ->orWhere('user_id_from', '=', $user->id);
        })->get();
        $return['user'] = auth('api')->user();
        $return['history'] = $transactions;
        return response()->json($return);
    }


    public function store(Request $request){
        $user = auth('api')->user();

        //se for lojista não prossegue
        if($user->user_type == 2){
            return response()->json(['status'=>0, 'message'=>'Lojista não tem permissão para transferir'], Response::HTTP_BAD_REQUEST);
        }

        if($user->id == $request->user_id_to){
            return response()->json(['status'=>0, 'message'=>'Você não pode enviar dinheiro para você mesmo'], Response::HTTP_BAD_REQUEST);
        }

        $messages  = [
            'transaction_type.required' => 'O tipo de envio obrigatório',
            'user_id_to.required' => 'Usuário receptor é obrigatório',
            'date.required' => 'Data é obrigatória',
            'amount.required' => 'Valor é obrigatório',
        ];

        $validator = \Validator::make($request->all(), [
            'transaction_type' => 'required',
            'user_id_to' => 'required',
            'date' => 'required',
            'amount' => 'required',
           
        ], $messages);

        //validação post
        if ($validator->fails()) {
            $responseArr['message'] = $validator->errors();
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        $transactionData = $request->only(['transaction_type', 'user_id_to','date','amount']);
        $transactionData['user_id_from'] = $user->id;
        $transactionData['amount'] = str_replace(',','.',str_replace(['R$',' ','.'], '', $request->amount));
        $transactionData['status'] = '0'; //pending

        try{

            $transaction = $this->startTransaction($user, $transactionData);

            if($this->getError()){
                return response()->json(['status'=>0, 'message'=> $this->getErrorMessage()], Response::HTTP_BAD_REQUEST);
            }

            //RESPONDE O ID DA TRANSAÇÃO PARA A VIEW CHAMAR O MÉTODO DE ENVIO DE E-MAIL
            $responseArr['message'] = "Transferência realizada com sucesso!";
            $responseArr['status'] = 1;
            $responseArr['data'] = ['transaction_id' => $transaction->id];
            return response()->json($responseArr);

        } catch (\Throwable $e) {
            
            report($e);
            $number = 0;
            if (property_exists($e, 'errorInfo')) {
                $number = $e->errorInfo[1];
            }
            
            $responseArr = ['status'=> 0, 'message'=>"houve um erro na execução do processo", 'number'=>$number];
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
         
        }

    }
    

    public function rollback(Request $request){
        
        $id = $request->transaction_id;
        if(empty($id) || $id <= 0){
            return response()->json(['status'=>0, 'message'=>'Favor informar o Id da Transação'], Response::HTTP_BAD_REQUEST);
        }

        $transaction = TransactionHistory::find($id);
        $data = $transaction->toArray();
        unset($data['user_id_to'], $data['user_id_from']);

        $user = auth('api')->user();

        //se for lojista não prossegue
        if($user->user_type == 2){
            return response()->json(['status'=>0, 'message'=>'Lojista não tem permissão para estornar Transferência'], Response::HTTP_BAD_REQUEST);
        }

        //verifica se a transação está finalizada
        if($transaction->status == 0){
            $responseArr['message'] = "Atenção, a Transferência informada não foi concretizada, não foi possivel prosseguir com sua solicitação";
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        //verifica se esta fazendo rollback de transação já estornada
        if($transaction->status == 2){
            $responseArr['message'] = "Atenção, essa Transferência já foi estornada anteriormente, não foi possivel prosseguir com sua solicitação";
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        try{
            //quem vai enviar, foi quem recebeu a transação
            $user = User::find($transaction->user_id_to);

            //quem vai receber, foi quem enviaou a transação
            $data['user_id_to'] = $transaction->user_id_from;
            $data['user_id_from'] = $transaction->user_id_to;
            $data['is_rollback'] = 1;
            $data['rollback_transaction_id'] = $transaction->id;

            $this->startTransaction($user, $data);

            $transaction->update(['status'=>2]);

            $responseArr['message'] = "Transferência estornada com sucesso!";
            $responseArr['status'] = 1;
            $responseArr['data'] = ['transaction_id' => $transaction->id];
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

    public function checkExternalAuthorization(){
        try{
            //URL para verificar Autorização
            $response = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
            $jsonReturn = $response->json();
            if($jsonReturn['message'] == "Autorizado"){
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            report($e);
            $number = 0;
            if (property_exists($e, 'errorInfo')) {
                $number = $e->errorInfo[1];
            }
            
            $this->setError('Um erro inesperado interrompeu o processo  (ExternalAuthorization: '.$number.')');
            return false;
        }
    }

    /**
     * Método privado que gera a transação/rollback
     */
    private function startTransaction($user, $transactionData){
        //dd($transactionData);
        if($transactionData['amount'] <= 0){
            $this->setError('Valor inválido');
            return false;
        }

        //VERIFICAÇÃO DE SALDO
        if(! Wallet::checkBalance($user, $transactionData['amount']) ){
            $this->setError('Não foi possivel completar sua solicitação, você não tem saldo suficiente para completar essa transação');
            return false;
        }

        //REGISTRA A SOLICITAÇÃO DE TRANSFERENCIA
        $transaction = TransactionHistory::create($transactionData);

        //Autorização
        if(! $this->checkExternalAuthorization()){
            $this->setError('Não foi possivel completar sua solicitação, não autorizado');
            return false;
        }

        //Atualizar Wallet
        //Poderia ser tbm via Trigger no BD (mais recomendado)
        Wallet::updateBalanceFrom($transactionData['user_id_from'],$transactionData['amount']);
        Wallet::updateBalanceTo($transactionData['user_id_to'],$transactionData['amount']);

        //se no BD a transaction não atualizar, é pq houve algum problema e precisa ser verificado
        $transaction->update(['status'=>1]);

        //para a notificação ser transparente podemos:
        //enviar a notificação para uma FILA
        //enviar a responde para a view que se encarrega de chamar um método de notificacao (escolhi essa)

        return $transaction;
    }

    public function setError($msg){
        $this->error = true;
        $this->errorMessage = $msg;
    }

    public function getError(){
        return $this->error;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }
}
