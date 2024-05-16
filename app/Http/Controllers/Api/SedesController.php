<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sedes\SedesListarRequest;
use App\Http\Requests\Sedes\SedesStoreRequest;
use App\Http\Requests\Sedes\SedesUpdateRequest;
use App\Http\Resources\SedesResource;
use App\Models\Sede;
use App\Models\Sedes;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PDO;
use Throwable;

class SedesController extends Controller
{
    use ResponseTrait;

/**
     * Función para listar todas las sedes
     * @OA\Get (
     *     path="/api/sedes",
     *     tags={"Sedes"},
     *     operationId="listSedes",
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

    //Función para listar todas las sedes
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
     * Función para crear un nuevo registro
     * @OA\Post (
     *     path="/api/sedes",
     *     tags={"Sedes"},
     *     operationId="InsertSedes",
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_sede", type="string", example="string"),
     *              @OA\Property(property="estado", type="boolean", example=true)
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Peticion realizada con exito",
     *     )
     * )
     */


    //Función para crear un nuevo registro
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $codigoPrefix = 'SE0';
            $nombreSede = $request->input('nombre_sede');
            $statement = DB::connection()->getPdo()->prepare('SELECT last_value FROM sedes_id_seq');
            $statement->execute();
            $idSede = $statement->fetchColumn();

            $codigoSede = $codigoPrefix . $idSede;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM sedes_list_create(:id_sede,:nombre,:codigo,:alias)');
            $query->bindParam(':id_sede', $idSede);
            $query->bindParam(':nombre', $nombreSede);
            $query->bindParam(':codigo', $codigoSede);
            $query->bindParam(':alias', $codigoPrefix);

            $sede = new Sede();

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
     * Función para ver detalle por Id
     * @OA\Get (
     *     path="/api/sedes/{id}",
     *     tags={"Sedes"},
     *     operationId="SelectSedesOfId",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID de la sede",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="ID de la sede", example=0),
     *              @OA\Property(property="codigo", type="string", description="Codigo de la sede", example="string"),
     *              @OA\Property(property="sede", type="string", description="Nombre de la sede", example="string"),
     *              @OA\Property(property="estado", type="boolean", description="Estado de la sede"),
     *              @OA\Property(property="alias", type="string", description="Alias de la sede", example="string")
     *         )
     *     )
     * )
     */

    //Función para ver detalle por Id
    public function show($id)
    {

        try {
            $query = DB::select('SELECT * FROM listar_sedes_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Función para actulizar registros
     * @OA\Put (
     *     path="/api/sedes/{id}",
     *     tags={"Sedes"},
     *     operationId="UpdateSedes",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la sede a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *              description="Datos de la sede a actualizar",
     *              @OA\JsonContent(
     *              @OA\Property(property="nombre_sede", type="string", example="string"),
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

    //Función para actulizar registros
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $sedes = Sede::find($id)->first();

            if (!$sedes) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT sedes_list_update(:id_sede, :nombre_sede)', [
                ':id_sede' => $id,
                ':nombre_sede' => $request->input('nombre_sede'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     *  Función para cambiar de estado de la sede
     *      @OA\Delete(
     *          path="/api/sedes/{id}",
     *          tags={"Sedes"},
     *          operationId="DeleteSedes",
     *      @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID de la sede",
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


    //Función para cambiar de estado
    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_estado_sedes(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
