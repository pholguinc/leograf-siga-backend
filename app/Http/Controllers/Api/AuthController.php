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
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login']]);
    }

    /**  Función para logease al sistema
     *  @OA\Post (
     *      path="/api/auth/login",
     *      tags={"Login"},
     *      operationId="AuthLogin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="captcha", type="string"),
     *              @OA\Property(property="confirm-captcha", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Se a logeado correctamente al sistema, bienvenido",
     *      ),
     *  )
     */

    public function login(LoginRequest $request)
    {
        try {
            if ($request->isJson() or $request->wantsJson() or $request->ajax()) {
                // Obtener las credenciales del request
                $credentials = $request->only('email', 'password');
                $credentials['estado'] = true;

                if ($request->input('captcha') !== $request->input('confirm-captcha')) {
                    return $this->responseErrorJson('Los campos de captcha no coinciden.', 422);
                }

                // Verificar si el usuario está bloqueado
                $blockTime = now()->subMinute(); // Timestamp actual menos 1 minuto
                $cacheKey = 'login_attempts_' . $request->ip();
                $failedAttempts = Cache::get($cacheKey, 0);
                $lastAttemptTime = Cache::get($cacheKey . '_time', null);

                if ($failedAttempts >= 6 && $lastAttemptTime && $lastAttemptTime > $blockTime) {
                    return $this->responseErrorJson('Demasiados intentos fallidos. Intente nuevamente más tarde.', 403);
                }

                // Intentar autenticar al usuario
                $token = Auth::guard('api')->attempt($credentials);

                if ($token) {
                    // Si la autenticación es exitosa, restablecer el contador de intentos fallidos
                    Cache::forget($cacheKey);
                    Cache::forget($cacheKey . '_time');
                    return $this->respondWithToken($token);
                }

                // Incrementar el contador de intentos fallidos y registrar la marca de tiempo del último intento
                $failedAttempts++;
                Cache::put($cacheKey, $failedAttempts, now()->addMinutes(1));
                Cache::put($cacheKey . '_time', now(), now()->addMinutes(1));

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

    /**
     *  @OA\Post(
     *      path="/api/auth/logout",
     *      tags={"Login"},
     *      security={{"bearer": {}}},
     *      operationId="AuthLogout",
     *   @OA\Response(
     *         response=200,
     *         description="Cerrastes sesion correctamente.",
     *     )
     *  ),
     */

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
