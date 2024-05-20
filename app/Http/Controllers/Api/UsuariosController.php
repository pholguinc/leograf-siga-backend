<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class UsuariosController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $idTipoDocumento = $request->input('id_tipo_documento');
            $numeroDocumento = $request->input('numero_documento');
            $nombres = $request->input('nombres');
            $apellidos = $request->input('apellidos');
            $email = $request->input('email');
            $fecha_nacimiento = $request->input('fecha_nacimiento');
            $id_genero = $request->input('id_genero');
            $id_codigo_pais =  $request ->input('id_codigo_pais');
            $celular =  $request->input('celular');

            $id_estado_civil = $request ->input('id_estado_civil');
            $direccion = $request-> input('direccion');
              
    

            $query = DB::connection()->getPdo()->prepare('SELECT * FROM usuarios_list_create(:id_tipo_documento,:numero_documento,
            :nombres, :apellidos, :email, :fecha_nacimiento, :id_genero, :id_codigo_pais, :celular, :id_estado_civil, :direccion)');
            $query->bindParam(':id_tipo_documento', $idTipoDocumento);
            $query->bindParam(':numero_documento', $numeroDocumento);
            $query->bindParam(':nombres', $nombres);
            $query->bindParam(':apellidos', $apellidos);
            $query->bindParam(':email', $email);
            $query->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $query->bindParam(':id_genero', $id_genero);
            $query->bindParam(':id_codigo_pais', $id_codigo_pais);
            $query->bindParam(':celular', $celular);
            $query->bindParam(':id_estado_civil', $id_estado_civil);
            $query->bindParam(':direccion', $direccion);


    

            $user = new User();

            $query->execute();
            $userData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();


            return $this->responseJson($userData);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //Función para ver detalle por Id
    public function show($id)
    {

        try {
            $query = DB::select('SELECT * FROM listar_usuarios_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro del usuario no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $usuarios = User::find($id)->first();

            if (!$usuarios) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT usuarios_list_update(:id_usuario, :id_tipo_documento, :numero_documento, :nombres, :apellidos, :email, :rol_id, :fecha_nacimiento, :id_genero, :id_codigo_pais, :celular, :id_estado_civil, :direccion)', [
                ':id_usuario' => $id,
                ':id_tipo_documento' => $request->input('id_tipo_documento'),
                ':numero_documento'=> $request->input('numero_documento'),
                ':nombres' => $request->input('nombres'),
                ':apellidos' => $request->input('apellidos'),
                ':email' => $request->input('email'),
                ':rol_id' => $request->input('rol_id'),
                ':fecha_nacimiento' => $request->input('fecha_nacimiento'),
                ':id_genero' => $request->input('id_genero'),
                ':id_codigo_pais' => $request->input('id_codigo_pais'),
                ':celular' => $request->input('celular'),
                ':id_estado_civil' => $request->input('id_estado_civil'),
                ':direccion' => $request->input('direccion'), 
                
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_estado_usuario(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
