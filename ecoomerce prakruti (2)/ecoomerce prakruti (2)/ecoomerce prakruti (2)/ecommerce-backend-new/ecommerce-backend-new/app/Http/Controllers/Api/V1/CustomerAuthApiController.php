<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerAuthApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // SECURITY RULE: Resolve Customer role by NAME. Never allow frontend to assign Admin/Super Admin!
        $customerRole = Role::firstOrCreate(['name' => 'Customer'], ['guard_name' => 'web']);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role_id' => $customerRole->id,
            'status' => 'active',
        ]);

        $tokenObj = $user->createToken('customer_api_token');

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully.',
            'data' => [
                'user' => new CustomerResource($user),
                'access_token' => $tokenObj->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        $passwordValid = false;
        try {
            if (Hash::check($request->password, $user->password)) {
                $passwordValid = true;
            }
        } catch (\Throwable $e) {
            $passwordValid = false;
        }

        // Fallback check for legacy plain-text stored passwords
        if (! $passwordValid && $request->password === $user->password) {
            $passwordValid = true;
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if (! $passwordValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        if (! $user->role || $user->role->name !== 'Customer') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only customer accounts can log in here.',
            ], 403);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => "Account is currently {$user->status}. Please contact support.",
            ], 403);
        }

        $tokenObj = $user->createToken('customer_api_token');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => new CustomerResource($user),
                'access_token' => $tokenObj->plainTextToken,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Token revoked.',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim((string) $request->email));
        $user = User::where('email', $email)->first();

        if ($user && $user->isCustomer()) {
            Password::sendResetLink(['email' => $email]);
        }

        return response()->json([
            'success' => true,
            'message' => 'If this customer email exists, a password reset link has been sent.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim((string) $request->email));
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'This reset link is invalid for customer login.',
            ], 422);
        }

        $status = Password::reset(
            [
                'email' => $email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => 'This reset link is invalid or expired.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please login with your new password.',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new CustomerResource(auth()->user()),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'phone']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new CustomerResource($user),
        ]);
    }
}
