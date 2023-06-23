<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                //return redirect(RouteServiceProvider::HOME);
                if(Auth::guard($guard)->user()->isActivated == 0){
                            
                    Auth::guard('api')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    // Auth::guard($guard)->logout();
                    // $request->session()->invalidate();
                    // $request->session()->regenerateToken();
                    // Auth::logout($request);
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Account not activated',
                    ], 401);
                }
                else{
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Method not allowed'
                    ], 405);
                }
                
            }
        }

        return $next($request);
    }
}