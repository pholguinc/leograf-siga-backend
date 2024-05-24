<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RecuperarContraseniaMail;
use App\Mail\SolicitudNuevaContraseniaMail;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDO;
use Throwable;

class EmailController extends Controller
{
    use ResponseTrait;
    public function solicitudNuevaContrasenia(Request $request)
    {
        try {
            DB::beginTransaction();

            $idTipoDocumento = $request->input('id_tipo_documento');
            $numeroDocumento = $request->input('numero_documento');
            $email = $request->input('email');
            $data = $request->all();
            $query = DB::connection()->getPdo()->prepare('SELECT * FROM email_solicitar_nueva_contrasenia(:id_tipo_documento,:numero_documento,:email)');
            $query->bindParam(':id_tipo_documento', $idTipoDocumento);
            $query->bindParam(':numero_documento', $numeroDocumento);
            $query->bindParam(':email', $email);
            $query->execute();
            $userData = $query->fetch(PDO::FETCH_ASSOC);

            Mail::to('holguinpedro90@gmail.com')->send(new SolicitudNuevaContraseniaMail($data, $userData));

            DB::commit();

            return response()->json([
                'message' => 'Solicitud enviada exitosamente. Se ha enviado un correo electrónico a la dirección proporcionada.',
                'status' => 'success'
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al enviar la solicitud. Intente nuevamente.',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function recuperarContrasenia(Request $request)
    {
        try {
            $idUsuario = $request->input('id');

            $password = $request->input('password');
            $captcha = $request->input('captcha');
            $confirmPassword = $request->input('confirm_password');

            if ($password !== $confirmPassword) {
                return $this->responseErrorJson('La contraseña de confirmación debe ser igual a la contraseña.');
            }

            $query = DB::select('SELECT * FROM listar_usuarios_por_id_list(:id)', [':id' => $idUsuario]);

            $userData =  $query[0];


            Mail::to('holguinpedro90@gmail.com')->send(new RecuperarContraseniaMail($userData, $request->all()));



            $hashedPassword = bcrypt($password);


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM usuarios_recuperar_contrasenia(:id,:password, :captcha)');
            $query->bindParam(':id', $idUsuario);
            $query->bindParam(':password', $hashedPassword);
            $query->bindParam(':captcha', $captcha);

            $query->execute();
            $passwordData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();



            return $this->responseJson($passwordData);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
