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
                // 'profilePicture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        if(Department::find($request->department) == null){
            return response()->json([
                'status' => 'failed',
                'message' => 'Department not found',
            ], 404);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->isActivated = false;
        $user->isAdmin = false;
        $user->save();

        // Get the uploaded file
        $profilePicture = $request->file('profilePicture');

        // Rename the file with the user id
        $newFileName = $user->id . '.' . $profilePicture->getClientOriginalExtension();

        // Store the file in the public folder
        $profilePicture->storeAs('images/', $newFileName, 'public');

        // Save the path in the database
        $user->profilePicture = $newFileName;
        $user->save();
        
        event(new Registered($user));

        $student = Student::create([
            'user_id' => $user->id,
            'department_id' => $request->department,
        ]);
        


        return response()->json([
            'status' => 'success',
            'message' => 'Your registration was successful.',
        ], 200);

    }
}