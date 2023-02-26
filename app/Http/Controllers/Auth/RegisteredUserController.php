<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use Illuminate\View\View;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'department' => ['int', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        
        event(new Registered($user));

        $role = $request->input('role');
        
        if ($role == "Student") {
            $student = Student::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'department_id' => $request->department,
            ]);
            event(new Registered($student));
        } else {
            $proffesor = Professor::create([
                'user_id' => $user->id,
                'name' => $request->name,
            ]);
            event(new Registered($proffesor));
        }

        //Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registered successfully'
        ]);

        //return redirect(RouteServiceProvider::HOME);
    }
}