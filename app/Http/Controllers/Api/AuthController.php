<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserAuthResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login']]);
    }

    public function login(LoginRequest $request)
    {
        try {
            if ($request->isJson() || $request->wantsJson() || $request->ajax()) {
                $credentials = $request->only('email', 'password');
                $credentials['estado'] = true;

                if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
                    return $this->responseErrorJson('Invalid email format.', 400);
                }

                $user = User::where('email', $credentials['email'])
                ->where('estado', true)
                    ->first();

                if (!$user) {
                    $user = User::where('email', $credentials['email'])->first(); 
                    if ($user) {
                        $user->increment('login_attempts'); 
                    }
                    return $this->responseErrorJson('Invalid credentials.', 401);
                }

                if (Auth::guard('api')->attempt($credentials, [], $this->shouldLockout($user))) {
                    $user->token()->delete();
                    return $this->respondWithToken($user->createToken('api_token'));
                }

                if ($this->shouldLockout($user)) {
                    return $this->responseErrorJson('Excediste el número de intentos. Prueba nuevamente en 5 minutos.', 403);
                }

                return $this->responseErrorJson('Credenciales Inválidas.', 401);
            }

            return $this->responseFormatInvalid();
        } catch (Throwable $e) {
            throw $e;
        }
    }

    protected function shouldLockout(User $user)
    {
        $loginAttempts = $user->login_attempts;

        $loginAttempts++;

        $user->login_attempts = $loginAttempts;
        $user->save();

        if ($loginAttempts >= config('auth.api.max_attempts', 6)) {

            return true;
        }

        return false; 
    }

    protected function respondWithToken($token)
    {
        $user = Auth::guard('api')->user();

        return $this->responseJson([
            'access_token'          => $token,
            'token_type'            => 'bearer',
            'expires_in'            => JWTAuth::factory()->getTTL() * 60,
            'usuario'               => new UserAuthResource($user),
 
        ]);
    }

    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
            return response()->json([
                'success' => true,
                'message' => 'El usuario se desconectó con éxito.'
            ]);
        } catch (Throwable $e) {
            throw $e;
        }
    }


}
