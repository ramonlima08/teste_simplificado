<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            
            $user = JWTAuth::parseToken()->authenticate();

        } catch (\Exception $e) {
            
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status'=> 0, 'message'=>'Token invalid']);
            }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status'=> 0, 'message'=>'Token Expired']);
            }else{
                return response()->json(['status'=> 0, 'message'=>'Autorization Token not found']);
            }
            
         
        }
        return $next($request);
    }
}
