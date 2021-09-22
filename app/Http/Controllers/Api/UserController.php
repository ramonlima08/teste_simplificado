<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Wallet;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Response;

class UserController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages  = [
            'name.required' => 'O nome é obrigatório',
            'email.required' => 'O e-mail é obrigatório',
            'email.unique' => 'Email ja registrado',
            'password.required' => 'A senha é obrigatória',
            'cpf_cnpj.required' => 'CPF/CNPJ é obrigatório',
            'cpf_cnpj.unique' => 'CPF/CNPJ ja registrado',
            'user_type.required' => 'o Tipo de Usuário é obrigatório',
        ];

        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'cpf_cnpj' => 'required|unique:users,cpf_cnpj',
            'user_type'=> 'required'
        ], $messages);
        
        //executando validação de campos
        if ($validator->fails()) {
            $responseArr['message'] = $validator->errors();
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        
        //removendo caracteres do cpf_cnpj
        $cpf_cnpj = preg_replace('/[^[:digit:]]/', '', $request->cpf_cnpj);
        if($cpf_cnpj <= 0){
            $responseArr['message'] = "Favor informar um CPF/CNPJ";
            $responseArr['status'] = 0;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        
        try{
            
            //criando o usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'password' => Hash::make($request->password),
                'cpf_cnpj' => $cpf_cnpj
            ]);

            //criando a carteira zerada
            Wallet::create([
                'user_id' => $user->id,
                'amount' => 0
            ]);

            //preparanto a resposta
            $responseArr['message'] = "Usuário cadastrado com sucesso";
            $responseArr['status'] = 1;
            $responseArr['data'] = $user;
            //resposta
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
    
    public function me(){
        return response()->json(auth('api')->user());
    }

}
