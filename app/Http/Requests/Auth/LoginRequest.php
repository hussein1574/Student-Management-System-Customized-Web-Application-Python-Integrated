<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "string", "email"],
            "password" => ["required", "string"],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request): JsonResponse
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only("email", "password");

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($this->throttleKey());

            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Invalid email or password",
                ],
                401
            );
        }

        if (Auth::user()->isActivated == false) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Your account is not activated",
                ],
                401
            );
        }

        $user = Auth::user();
        $accessToken = $user->createToken("AuthToken")->accessToken;
        $user["token"] = $accessToken;
        $response = [
            "success" => true,
            "message" => "Login success",
            "token" => $accessToken,
        ];
        return response()->json($response);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            "email" => trans("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->input("email")) . "|" . $this->ip()
        );
    }
}
