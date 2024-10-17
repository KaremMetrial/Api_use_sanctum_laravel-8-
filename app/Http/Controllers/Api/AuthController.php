<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;

    class AuthController extends Controller
    {
        public function register(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone|regex:/^[0-9]{10,15}$/',
                'password' => 'required|string|min:8'
            ]);

            $verificationCode = rand(100000, 999999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'verification_code' => $verificationCode,
                'is_verified' => false,
            ]);

            Log::info('Verification Code for user ' . $user->name . ' : ' . $verificationCode);

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'verification_code' => $verificationCode
                ],
                'message' => 'User registered successfully. Please verify your account.'
            ]);
        }

        public function login(Request $request)
        {
            $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            $user = User::where('phone', $request->phone)->first();

            if (!$user || !Hash::check($request->password, $user->password) || !$user->is_verified) {
                return response()->json(['message' => 'Unauthorized. Please verify your account or invalid credentials.'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        public function verifyCode(Request $request)
        {
            $request->validate([
                'phone' => 'required|string|exists:users,phone',
                'verification_code' => 'required|string|size:6',
            ]);

            $user = User::where('phone', $request->phone)->first();

            if ($user->verification_code !== $request->verification_code) {
                return response()->json(['message' => 'Invalid verification code.'], 401);
            }

            $user->is_verified = true;
            $user->verification_code = null;
            $user->save();

            return response()->json(['message' => 'Verification successful.']);
        }

        public function user(Request $request)
        {

            return response()->json($request->user());
        }

        public function logout(Request $request)
        {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        }
    }
