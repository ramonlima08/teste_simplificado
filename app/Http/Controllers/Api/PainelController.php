<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class PainelController extends Controller
{
    public function permission(Request $request){
        
        try{

            $user = auth('api')->user();
        
            $permissions = User::getPermissions($user);
            return response()->json($permissions);

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
