<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        try {
            if ($request->isJson() or $request->wantsJson() or $request->ajax()) {

                $credentials            = $request->only('email', 'password');
                $credentials['estado']  = true;
                $token = Auth::guard('api')->attempt($credentials);

                if ($token) {
                    return $this->respondWithToken($token);
                }
                return $this->responseErrorJson('Credenciales inválidas', 401);
            }
            return $this->responseFormatInvalid();
        } catch (Throwable $e) {
            throw $e;
        }
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
