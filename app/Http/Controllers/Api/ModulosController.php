<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modulos\ModulosStoreRequest;
use App\Http\Requests\Modulos\ModulosUpdateRequest;
use App\Http\Resources\ModulosResource;
use App\Models\Modulo;
use App\Models\Modulos;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class ModulosController extends Controller
{

    use ResponseTrait;

    /**
     * Función para listar todos modulos
     * @OA\Get (
     *     path="/api/modulo",
     *     tags={"Modulos"},
     *     operationId="listModulos",
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

            $query = DB::select('SELECT * FROM listar_modulos_grid_list(:offset, :limit, :nombre, :estado);', [
                'offset' => $offset,
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
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para crear un nuevo modulos
     * @OA\Post (
     *     path="/api/modulo",
     *     tags={"Modulos"},
     *     operationId="InsertModulos",
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos del modulo a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_modulo", type="string", example="modulo nuevo"),
     *              @OA\Property(property="image_url", type="string", example="imagen.png")
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

            $codigoPrefix = 'MO';
            $nombreModulo = $request->input('nombre_modulo');
            $image_url = $request->input('image_url');
            $statement = DB::connection()->getPdo()->prepare('SELECT nextval(\'modulos_id_seq\')');
            $statement->execute();
            $idModulo = $statement->fetchColumn();

            $codigoModulo = $codigoPrefix . $idModulo;




            $query = DB::connection()->getPdo()->prepare('SELECT * FROM modulos_list_create(:id_modulo,:codigo,:nombre,:alias, :image_url)');
            $query->bindParam(':id_modulo', $idModulo);
            $query->bindParam(':nombre', $nombreModulo);
            $query->bindParam(':codigo', $codigoModulo);
            $query->bindParam(':alias', $codigoPrefix);
            $query->bindParam(':image_url', $image_url);

            $sede = new Modulo();

            $query->execute();
            $moduloData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();


            return $this->responseJson($moduloData);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Función para ver detalles por modulos
     * @OA\Get (
     *     path="/api/modulo/{id}",
     *     tags={"Modulos"},
     *     operationId="SelectModulosOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del Modulo",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="ID del modulo", example=0),
     *              @OA\Property(property="codigo", type="string", description="Codigo del modulo", example="codigo"),
     *              @OA\Property(property="modulo", type="string", description="Nombre del modulo", example="modulo"),
     *              @OA\Property(property="alias", type="string", description="Alias del modulo", example="alias"),
     *              @OA\Property(property="imageurl", type="string", description="Imagen del modulo", example="imagen.jpg"),
     *              @OA\Property(property="estado", type="boolean", description="Estado del modulo"),
     *         )
     *     )
     * )
     */

    public function show($id)
    {

        try {
            $query = DB::select('SELECT * FROM listar_modulos_por_id_list(:id)', [':id' => $id]);

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
     *     path="/api/modulo/{id}",
     *     tags={"Modulos"},
     *     operationId="UpdateModulo",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del modulo a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *                  @OA\Property(property="nombre_modulo", type="string", example="modulo nuevo"),
     *                  @OA\Property(property="image_url", type="string", example="imagen.png"),
     *                  @OA\Property(property="estado", type="boolean")
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

            $modulos = Modulo::find($id)->first();

            if (!$modulos) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT modulos_list_update(:id_modulo, :nombre_modulo, :image_url)', [
                ':id_modulo' => $id,
                ':nombre_modulo' => $request->input('nombre_modulo'),
                ':image_url' => $request->input('image_url'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     *  Función para cambiar de estado del modulos
     *      @OA\Delete(
     *          path="/api/modulo/{id}",
     *          tags={"Modulos"},
     *          operationId="DeleteModulos",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID del modulo",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Modulo no encontrado"
     *      )
     *  )
     */

    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_estado_modulos(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
