<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

            $query = DB::table('sedes');

            // Filtrar por el parámetro nombre
            if ($request->filled('nombre')) {
                $nombre = $request->input('nombre');
                $query->where('nombre', 'like', '%' . $nombre . '%');
            }

            // Filtrar por el parámetro estado
            if ($request->filled('estado')) {
                $estado = $request->input('estado');


                if ($estado == 'true') {
                    $query->where('estado', true);
                } elseif ($estado == 'false') {
                    $query->where('estado', false);
                }
            }
            $per_page   = $request->input('per_page', 10);
            $data       = $query->paginate($per_page);
            $aux = SedesResource::collection($data);

            $data = [
                'data' => $aux,
                'pagination' => [
                    'total' => $aux->total(),
                    'current_page' => $aux->currentPage(),
                    'per_page' => $aux->perPage(),
                    'last_page' => $aux->lastPage(),
                    'from' => $aux->firstItem(),
                    'to' => $aux->lastItem()
                ]

            ];

            return $this->responseJson($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function store(SedesStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $sedes = new Sedes();
            $sedes->nombre = $request->nombre;
            $sedes->estado = $request->estado;
            $sedes->save();

            DB::commit();

            $data = new SedesResource($sedes);

            return $this->responseJson($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        try {
            $sede = Sedes::where('id', $id)->first();

            if (!$sede) {

                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $data = new SedesResource($sede);

            return $this->responseJson($data);
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
            // Devolvemos con mensaje
            return $this->responseJson($data);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $sede = Sedes::where('id', $id)->first();

            if (!$sede) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            // Cambiar el estado entre true y false
            $sede->update(['estado' => !$sede->estado]);

            $data = [
                'message' => 'Sede Eliminada correctamente',
                'status' => 200,
            ];
            return $this->noContent();
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
