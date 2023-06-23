<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Termwind\Components\Dd;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use PgSql\Lob;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return response()->json([
            'status' => 'failed',
            'message' => 'Unauthenticated',
        ],401);
      //  return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        
        //$request->authenticate();
        $response =  $request->authenticate($request);
        if($response->getStatusCode() != 401)
            $request->session()->regenerate(); 
  
        return $response;
        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // remove the token
        $user = Auth::user()->token();
        $user->revoke();
        
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}