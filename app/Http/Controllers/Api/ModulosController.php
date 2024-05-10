<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modulos\ModulosStoreRequest;
use App\Http\Requests\Modulos\ModulosUpdateRequest;
use App\Http\Resources\ModulosResource;
use App\Models\Modulos;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ModulosController extends Controller
{

    use ResponseTrait;
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
    public function store(ModulosStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $modulo = new Modulos();
            $modulo->nombre = $request->nombre;
            $modulo->estado = $request->estado;

            $codigoPrefix = '000';
            $modulo->codigo = $codigoPrefix;

            $modulo->save();

            $modulo->codigo = $codigoPrefix . $modulo->id_modulo;
            $modulo->save();


            DB::commit();

            $messages = [
                'message' => 'Registro exitoso',
                'status' => 200
            ];

            return response()->json($messages);
        } catch (Throwable $e) {
            throw $e;
        }
    }

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

    public function update(ModulosUpdateRequest $request, $id)
    {
        
        try {
            DB::beginTransaction();

            $modulo = Modulos::find($id);

            if (!$modulo) {
                $data = [
                    'message' => 'Registro no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $modulo->update([
                'nombre' => $request->nombre,
                'estado' => $request->estado
            ]);

            $messages = [
                'message' => 'Registro actualizado',
                'status' => 200,
            ];

            DB::commit();

            return response()->json($messages);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

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
