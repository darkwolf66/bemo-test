<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(array_key_exists('access_token', $request->all())){
            $token = $request->all()['access_token'];
        }else{
            $token = $request->bearerToken();
        }
        $user = User::where('access_token', $token);

        if($user->count() > 0){
            Auth::login($user->first());
            return $next($request);
        }else{
            return response()->json('invalid_token', 403);
        }
    }
}
