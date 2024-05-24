<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuStoreRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class MenuController extends Controller
{
    use ResponseTrait;

    /**
     * Función para listar todos modulos
     * @OA\Get (
     *     path="/api/menus",
     *     tags={"Menus"},
     *     operationId="listMenus",
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     )
     * )
     */

    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
        }
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $idModulo = $request->input('id_modulo');
        $nombreSede = $request->input('nombre');
        $estadoSede = $request->input('estado');

        $query = DB::select('SELECT * FROM listar_menus_grid_list(:offset, :limit, :id_modulo, :nombre, :estado);', [
            'offset' => $offset,
            'id_modulo' => $idModulo,
            'limit' => $limit,
            'nombre' => $nombreSede,
            'estado' => $estadoSede
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
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $menus = Menu::find($id)->first();

            if (!$menus) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT menus_list_update(:id_menu, :menu, :modulo_id)', [
                ':id_menu' => $id,
                ':menu' => $request->input('nombre_menu'),
                ':modulo_id' => $request->input('id_modulo'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Función para crear un nuevo menu
     * @OA\Post (
     *     path="/api/menus",
     *     tags={"Menus"},
     *     operationId="InsertMenu",
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos del menu a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_menu", type="string", example="menu nuevo"),
     *              @OA\Property(property="id_modulo", type="integer", example=0)
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

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->responseErrorJson('Token de autorización no encontrado', [], 401);
            }

            $codigoPrefix = 'ME0';
            $nombreMenu = $request->input('nombre_menu');
            $moduloId = $request->input('id_modulo');
            $statement = DB::connection()->getPdo()->prepare('SELECT nextval(\'menus_id_seq\')');
            $statement->execute();
            $idMenu = $statement->fetchColumn();

            $moduloAliasQuery = DB::table('modulos')->where('id', $moduloId)->select('alias')->first();
            $moduloAlias = $moduloAliasQuery ? $moduloAliasQuery->alias : '';



            $codigoMenu = $moduloAlias . $moduloId . $codigoPrefix . $idMenu;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM menus_list_create(:id_menu,:nombre,:codigo,:alias, :moduloId)');
            $query->bindParam(':id_menu', $idMenu);
            $query->bindParam(':nombre', $nombreMenu);
            $query->bindParam(':codigo', $codigoMenu);
            $query->bindParam(':alias', $codigoPrefix);
            $query->bindParam(':moduloId', $moduloId);

            $sede = new Menu();

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
     * Función para ver detalles por menus
     * @OA\Get (
     *     path="/api/menus/{id}",
     *     tags={"Menus"},
     *     operationId="SelectMenuOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del Menu",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="ID del menu", example=0),
     *              @OA\Property(property="codigo", type="string", description="Codigo del menu", example="codigo"),
     *              @OA\Property(property="menu", type="string", description="Nombre del menu", example="menu"),
     *              @OA\Property(property="alias", type="string", description="Alias del menu", example="alias"),
     *              @OA\Property(property="estado", type="boolean", description="Estado del menu"),
     *              @OA\Property(property="modulo_id", type="number", description="Numero de modulo", example=0),
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
            $query = DB::select('SELECT * FROM listar_menus_por_id_list(:id)', [':id' => $id]);

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
     *          path="/api/menus/{id}",
     *          tags={"Menus"},
     *          operationId="DeleteMenu",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del menu",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Menu no encontrado"
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
            $query = DB::select('SELECT * FROM cambiar_estado_menus(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
