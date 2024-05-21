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

    /**
     * Función para listar todos los usuarios
     * @OA\Get (
     *     path="/api/usuarios",
     *     tags={"Usuarios"},
     *     operationId="listUsuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="offset",
     *                         type="number",
     *                         example="0"
     *                     ),
     *                     @OA\Property(
     *                         property="limit",
     *                         type="number",
     *                         example="0"
     *                     ),
     *                     @OA\Property(
     *                         property="nombre",
     *                         type="string",
     *                         example="string"
     *                     ),
     *                     @OA\Property(
     *                         property="estado",
     *                         type="boolean",
     *                         example="true"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        try {
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $nombreSede = $request->input('nombre');
            $estadoSede = $request->input('estado');

            $query = DB::select('SELECT * FROM listar_sedes_grid_list(:offset, :limit, :nombre, :estado);', [
                'offset' => $offset,
                'limit' => $limit,
                'nombre' => $nombreSede ? "%{$nombreSede}%" : null,
                'estado' => $estadoSede
            ]);

            $data = [
                'data' => $query,
                'pagination' => [
                    'total' => count($query),
                    'current_page'
                    => (int) ceil($offset / $limit),
                    'per_page' => $limit,
                    'last_page' => (int) ceil(count($query) / $limit),
                    'from' => $offset + 1,
                    'to' => min($offset + $limit, count($query)),
                ]
            ];

            return $this->responseJson($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para crear un nuevo usuario
     * @OA\Post (
     *     path="/api/usuarios",
     *     tags={"Usuarios"},
     *     operationId="InsertUsuarios",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Datos de la sede a actualizar",
     *          @OA\JsonContent(
     *              @OA\Property(property="id_tipo_documento", type="integer", example=1),
     *              @OA\Property(property="numero_documento", type="string", example="string"),
     *              @OA\Property(property="nombres", type="string", example="string"),
     *              @OA\Property(property="apellidos", type="string", example="string"),
     *              @OA\Property(property="email", type="string", example="example@email.com"),
     *              @OA\Property(property="fecha_nacimiento", type="date"),
     *              @OA\Property(property="id_genero", type="integer", example=1),
     *              @OA\Property(property="id_codigo_pais", type="string", example="string"),
     *              @OA\Property(property="celular", type="string", example="string"),
     *              @OA\Property(property="id_estado_civil", type="integer", example=1),
     *              @OA\Property(property="direccion", type="string", example="string")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     * )
     */

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

    /**
     * Función para ver detalle por Id
     * @OA\Get (
     *     path="/api/usuarios/{id}",
     *     tags={"Usuarios"},
     *     operationId="SelectUsuariosOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id_tipo_documento", type="integer", example=1),
     *              @OA\Property(property="numero_documento", type="string", example="string"),
     *              @OA\Property(property="nombres", type="string", example="string"),
     *              @OA\Property(property="apellidos", type="string", example="string"),
     *              @OA\Property(property="email", type="string", example="example@email.com"),
     *              @OA\Property(property="fecha_nacimiento", type="date"),
     *              @OA\Property(property="id_genero", type="integer", example=1),
     *              @OA\Property(property="id_codigo_pais", type="string", example="string"),
     *              @OA\Property(property="celular", type="string", example="string"),
     *              @OA\Property(property="id_estado_civil", type="integer", example=1),
     *              @OA\Property(property="direccion", type="string", example="string"),
     *         ),
     *     ),
     * )
     */

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

    /**
     * Función para actulizar los modulos
     * @OA\Put (
     *     path="/api/usuarios/{id}",
     *     tags={"Usuarios"},
     *     operationId="UpdateUsuario",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Datos del usuario a actualizar",
     *          @OA\JsonContent(
     *              @OA\Property(property="id_tipo_documento", type="integer", example=1),
     *              @OA\Property(property="numero_documento", type="string", example="string"),
     *              @OA\Property(property="nombres", type="string", example="string"),
     *              @OA\Property(property="apellidos", type="string", example="string"),
     *              @OA\Property(property="email", type="string", example="example@email.com"),
     *              @OA\Property(property="fecha_nacimiento", type="date"),
     *              @OA\Property(property="id_genero", type="integer", example=1),
     *              @OA\Property(property="id_codigo_pais", type="string", example="string"),
     *              @OA\Property(property="celular", type="string", example="string"),
     *              @OA\Property(property="id_estado_civil", type="integer", example=1),
     *              @OA\Property(property="direccion", type="string", example="string"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sede actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sede no encontrada"
     *     )
     * )
     */

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

    /**
     *  Función para cambiar de estado
     *      @OA\Delete(
     *          path="/api/usuarios/{id}",
     *          tags={"Usuarios"},
     *          operationId="DeleteUsuario",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Sede no encontrado"
     *      )
     *  )
 */

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
