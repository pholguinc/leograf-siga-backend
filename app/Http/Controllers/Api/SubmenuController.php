<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Submenu\SubmenuStoreRequest;
use App\Http\Requests\Submenu\SubmenuUpdateRequest;
use App\Models\Submenu;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class SubmenuController extends Controller
{
    use ResponseTrait;

    /**
     * Función para listar todas las sedes
     * @OA\Get(
     *     path="/api/submenus",
     *     tags={"Submenus"},
     *     operationId="listSubmenus",
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
     *                         property="submenu_nombre",
     *                         type="string",
     *                         example="string"
     *                     ),
     *                     @OA\Property(
     *                         property="id_modulo",
     *                         type="integer",
     *                         example=0
     *                     ),
     *                     @OA\Property(
     *                         property="id_menu",
     *                         type="integer",
     *                         example=0
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
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }
            
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $nombreSubmenu = $request->input('submenu_nombre');
            $idModulo = $request->input('id_modulo');
            $idMenu = $request->input('id_menu');
            $estadoSubmenu = $request->input('estado');

            $query = DB::select('SELECT * FROM listar_submenus_grid_list(:offset, :limit, :id_modulo, :id_menu, :submenu_nombre, :estado);', [
                'offset' => $offset,
                'limit' => $limit,
                'id_modulo'=> $idModulo,
                'id_menu' => $idMenu,
                'submenu_nombre' => $nombreSubmenu,
                'estado' => $estadoSubmenu
            ]);

            $data = [
                'data' => $query,
                'pagination' => [
                    'total' => count($query),
                    'current_page' => (int) $offset / $limit + 1,
                    'per_page' => $limit,
                    'last_page' => (int) ceil(count($query) / $limit),
                    'from' => $offset + 1,
                    'to' => min($offset + $limit, count($query)),
                ]
            ];

            return response()->json($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para crear un nuevo modulos
     * @OA\Post (
     *     path="/api/submenus",
     *     tags={"Submenus"},
     *     operationId="InsertSubmenus",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Datos del submenus a actualizar",
     *          @OA\JsonContent(
     *              @OA\Property(property="nombre_submenu", type="string", example="Submenu_nuevo"),
     *              @OA\Property(property="id_menu", type="number"),
     *              @OA\Property(property="id_modulo", type="number"),
     *
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     )
     * )
     */

    public function store(SubmenuStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $codigoPrefix = 'SU0';
            $nombreSubMenu = $request->input('nombre');
            $menuId = $request->input('id_menu');
            $moduloId = $request->input('id_modulo');
            $moduloEstado = $request->input('estado');
            $statement = DB::connection()->getPdo()->prepare('SELECT nextval(\'submenus_id_seq\')');
            $statement->execute();
            $idSubMenu = $statement->fetchColumn();

            $moduloAliasQuery = DB::table('modulos')->where('id', $moduloId)->select('alias')->first();
            $moduloAlias = $moduloAliasQuery ? $moduloAliasQuery->alias : '';

            $menuAliasQuery = DB::table('menus')->where('id', $menuId)->select('alias')->first();
            $menuAlias = $menuAliasQuery ? $menuAliasQuery->alias : '';


            $codigoSubMenu = $moduloAlias . $moduloId . $menuAlias . $menuId. $codigoPrefix . $idSubMenu;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM submenus_list_create(:id_submenu,:codigo,:nombre, :id_menu, :id_modulo, :estado)');
            $query->bindParam(':id_submenu', $idSubMenu);
            $query->bindParam(':codigo', $codigoSubMenu);
            $query->bindParam(':nombre', $nombreSubMenu);
            $query->bindParam(':id_menu', $menuId);
            $query->bindParam(':id_modulo', $moduloId);
            $query->bindParam(':estado', $moduloEstado);

            $submenu = new Submenu();

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
     * Función para ver detalles por modulos
     * @OA\Get (
     *     path="/api/submenus/{id}",
     *     tags={"Submenus"},
     *     operationId="SelectSubmenuOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del Submenu",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="ID del submenu", example=0),
     *              @OA\Property(property="codigo", type="string", description="Codigo del submenu", example="codigo"),
     *              @OA\Property(property="submenu", type="string", description="Nombre del submenu", example="submenu"),
     *              @OA\Property(property="menu_id", type="integer", description="Imagen del submenu", example=0),
     *              @OA\Property(property="estado", type="boolean", description="Estado del submenu"),
     *         )
     *     )
     * )
     */

    public function show($id)
    {

        try {

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $query = DB::select('SELECT * FROM listar_submenus_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     *  Función para cambiar de estado del modulos
     *      @OA\Delete(
     *          path="/api/submenus/{id}",
     *          tags={"Submenus"},
     *          operationId="DeleteSubmenus",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del submenus",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Submenu no encontrado"
     *      )
     *  )
     */

    public function delete($id)
    {
        try {

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $query = DB::select('SELECT * FROM cambiar_estado_submenus(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para actulizar los modulos
     * @OA\Put (
     *     path="/api/submenus/{id}",
     *     tags={"Submenus"},
     *     operationId="UpdateSubmenu",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del Submenu a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *                  @OA\Property(property="nombre_submenu", type="string", example="Submenu_nuevo"),
     *                  @OA\Property(property="estado", type="boolean"),
     *                  @OA\Property(property="id_menu", type="number")
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

    public function update(SubmenuUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $submenus = Submenu::find($id)->first();

            if (!$submenus) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT submenus_list_update(:id_submenu, :nombre_submenu, :id_menu, :id_modulo)', [
                ':id_submenu' => $id,
                ':nombre_submenu' => $request->input('nombre_submenu'),
                ':id_menu' => $request->input('id_menu'),
                ':id_modulo' => $request->input('id_modulo'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }




}
