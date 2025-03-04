<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function Termwind\render;

class CheckIfAdmin
{
    /**
     * Checked that the logged in user is an administrator.
     *
     * --------------
     * VERY IMPORTANT
     * --------------
     * If you have both regular users and admins inside the same table, change
     * the contents of this method to check that the logged in user
     * is an admin, and not a regular user.
     *
     * Additionally, in Laravel 7+, you should change app/Providers/RouteServiceProvider::HOME
     * which defines the route where a logged in user (but not admin) gets redirected
     * when trying to access an admin route. By default it's '/home' but Backpack
     * does not have a '/home' route, use something you've built for your users
     * (again - users, not admins).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    private function checkIfUserIsAdmin($user)
    {
        $isAdmin = DB::table('users')->where('email', $user->email)->first()->isAdmin;
        return $isAdmin;
    }
    private function checkIfActivated($user)
    {
        $isActivated = DB::table('users')->where('email', $user->email)->first()->isActivated;
        return $isActivated;
    }

    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        //dd($request);
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->guest(backpack_url('login'));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }
        if (!$this->checkIfUserIsAdmin(backpack_user())) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Alert::error('Wrong email or password')->flash();
            return redirect()->guest(backpack_url('login'));
        }
        if(!$this->checkIfActivated(backpack_user())){
            Auth::guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Alert::error('Account is not activated')->flash();
            return redirect()->guest(backpack_url('login'));
        }

        return $next($request);
    }
}