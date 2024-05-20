<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\RolStoreRequest;
use App\Http\Requests\Roles\RolUpdateRequest;
use App\Http\Resources\RolesResource;
use App\Models\Rol;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class RolController extends Controller
{
    use ResponseTrait;

    /**
     * Función para listar todos los roles
     * @OA\Get (
     *     path="/api/rol",
     *     tags={"Roles"},
     *     operationId="listRoles",
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
            $nombreRol = $request->input('rol_nombre');
            $estadoRol = $request->input('estado');

            $query = DB::select('SELECT * FROM listar_roles_grid_list(:offset, :limit, :rol_nombre, :estado);', [
                'offset' => $offset,
                'limit' => $limit,
                'rol_nombre' => $nombreRol ? "%{$nombreRol}%" : null,
                'estado' => $estadoRol
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
     * Función para crear un nuevo rol
     * @OA\Post (
     *     path="/api/rol",
     *     tags={"Roles"},
     *     operationId="InsertRoles",
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_rol", type="string", example="string"),
     *              @OA\Property(property="estado", type="boolean", example=true)
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $codigoPrefix = 'RO0';
            $nombreRol = $request->input('nombre_rol');
            $statement = DB::connection()->getPdo()->prepare('SELECT nextval(\'roles_id_seq\')');
            $statement->execute();
            $idRol = $statement->fetchColumn();

            $codigoRol = $codigoPrefix . $idRol;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM roles_list_create(:id_rol,:nombre,:codigo)');
            $query->bindParam(':id_rol', $idRol);
            $query->bindParam(':nombre', $nombreRol);
            $query->bindParam(':codigo', $codigoRol);


            $rol = new Rol();

            $query->execute();
            $sedeData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();


            return $this->responseJson($sedeData);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Función para agregar permisos a los roles
     * @OA\Post (
     *     path="/api/rol/permisos",
     *     tags={"Roles"},
     *     operationId="InsertPermisosRoles",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Asignacion de roles",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="rol_id",
     *                  type="integer",
     *                  description="ID del rol"
     *              ),
     *              @OA\Property(
     *                  property="permisos_ids",
     *                  type="array",
     *                   @OA\Items(
     *                      type="integer"
     *                   )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     )
     * )
     */

    public function permisos(Request $request){
        try {
            DB::beginTransaction();
            $rolId = $request->input('rol_id');
            $permisosIds = $request->input('permisos_ids');


            $pdo = DB::connection()->getPdo();
            $query = $pdo->prepare('SELECT * FROM asignar_rol_permisos(:rolId, :permisosIds)');
        

            foreach ($permisosIds as $permisoId) {
                $query->bindParam(':rolId', $rolId);
                $query->bindParam(':permisosIds', $permisoId);
                $query->execute();
            }



            if (
                config('app.debug') || 
                $request->has('withPermissions')
            ) {
                $permisos = DB::table('permisos')
                ->join('rol_permisos', 'rol_permisos.permiso_id', '=', 'permisos.id')
                ->where('rol_permisos.rol_id', $rolId)
                ->select('permisos.*')
                ->get();
            } else {
                $permisos = null;
            }


            DB::commit();

            $messages = [
                'message' => 'Los permiso fueron asignados correctamente.',
            ];


            return $this->responseJson($messages);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Función para ver detalles por rol
     * @OA\Get (
     *     path="/api/rol/{id}",
     *     tags={"Roles"},
     *     operationId="SelectRolOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="ID del rol", example=0),
     *              @OA\Property(property="nombre", type="string", description="Nombre del rol", example="string"),
     *              @OA\Property(property="codigo", type="string", description="Codigo del rol", example="string"),
     *              @OA\Property(property="estado", type="boolean", description="Estado del rol"),
     *         )
     *     )
     * )
     */

    public function show($id)
    {

        try {
            $query = DB::select('SELECT * FROM listar_roles_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para actulizar los roles
     * @OA\Put (
     *     path="/api/rol/{id}",
     *     tags={"Roles"},
     *     operationId="UpdateRol",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_rol", type="string", example="string"),
     *              @OA\Property(property="estado", type="boolean", example=true)
     *          )
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

            $roles = Rol::find($id)->first();

            if (!$roles) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT roles_list_update(:id_rol, :nombre_rol)', [
                ':id_rol' => $id,
                ':nombre_rol' => $request->input('nombre_rol'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     *  Función para cambiar de estado del rol
     *      @OA\Delete(
     *          path="/api/rol/{id}",
     *          tags={"Roles"},
     *          operationId="DeleteRol",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Rol no encontrado"
     *      )
     *  )
     */

    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_estado_roles(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }


}
