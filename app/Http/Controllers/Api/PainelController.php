<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class PainelController extends Controller
{
    public function permission(Request $request){
        $user = auth('api')->user();
        
        $permissions = User::getPermissions($user);
        return response()->json($permissions);
    }
}
