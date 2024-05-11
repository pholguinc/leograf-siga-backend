<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sedes\SedesListarRequest;
use App\Http\Requests\Sedes\SedesStoreRequest;
use App\Http\Requests\Sedes\SedesUpdateRequest;
use App\Http\Resources\SedesResource;
use App\Models\Sedes;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class SedesController extends Controller
{
    use ResponseTrait;

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

            return $this->responseJson($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function store(Request $request)
    {

        try {

            $idSede = $request->input('id');
            $nombreSede = $request->input('nombre');
            
            $query = DB::select('SELECT * FROM add_upd_sedes_list(:id_sede,:nombre);', [
                'id_sede' => $idSede,
                'nombre' => $nombreSede,
              
            ]);

            return $this->responseJson($query);

        } catch (Throwable $e) {
            throw $e;
        }
    }

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

    public function update(SedesUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $sedes = Sedes::find($id)->first();

            if (!$sedes) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $updateData = [];

            if ($request->filled('nombre')) {
                $updateData['nombre'] = $request->nombre;
            }

            if (!is_null($request->estado)) {
                $updateData['estado'] = $request->estado;
            }

            $sedes->update($updateData);

            $data = new SedesResource($sedes);

            DB::commit();
            return $this->responseJson($data);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_Estado_sedes(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
