<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 401);
        }
        $credential = $validator->validated();
        if (Auth::attempt($credential)) {
            $user = Auth::user();
            $payload = [
                'iss' => 'ars',
                'role' => $user->role,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 3600
            ];
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
            return response()->json([
                'message' => 'berhasil login', 
                'Bearer' => $jwt
            ], 200);
        }
        return response()->json([
            'message' => 'email atau password salah',
        ], 401);
    }

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' =>  $validator->errors()
            ], 401);
        }
        $credential = $validator->validated();
        $credential['password'] = bcrypt($credential['password']);
        $user = User::create($credential);
        $payload = [
            'iss' => 'ars',
                'role' => $user->role,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 3600
        ];
        $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
            return response()->json([
                'message' => 'berhasil register dan login', 
                'Bearer' => $jwt
            ], 200);
    }
    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function callbackGoogle()
    {
        try {
            $user = Socialite::driver('google')->user();
            $cekUser = User::where('email', $user->email)->first();
            if ($cekUser) {
                Auth::login($cekUser);
                $payload = [
                    'iss' => 'ars',
                    'role' => $user->role,
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'iat' => now()->timestamp,
                    'exp' => now()->timestamp + 3600
                ];
                $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
                return response()->json([
                    'message' => 'berhasil login',
                    'Bearer' => $jwt
                ], 200);
            }
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt('haris123'. $user->email. $user->name)
            ]);
            $payload = [
                'iss' => 'ars',
                'role' => $user->role,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 3600
            ];
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
            return response()->json([
                'message' => 'berhasil register dan login',
                'Bearer' => $jwt
            ], 200);
        } catch (\Exception $e) {
            return redirect()->away('http://127.0.0.1:8000/api/oauth/register');
        }
    }
}