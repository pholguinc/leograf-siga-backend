<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sedes\SedesStoreRequest;
use App\Http\Requests\Sedes\SedesUpdateRequest;
use App\Http\Resources\SedesResource;
use App\Models\Sedes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class SedesController extends Controller
{
    public function store(SedesStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $sedes = new Sedes();
            $sedes->nombre = $request->nombre;
            $sedes->estado = $request->estado;
            $sedes->save(); 

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

    public function index(Request $request){
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
                }
               
                elseif ($estado == 'false') {
                    $query->where('estado', false);
                }
            }
            $per_page   = $request->input('per_page', 10);
            $data       = $query->paginate($per_page);
            $aux = SedesResource::collection($data);
            
            $data = [
                'data' => $aux,
                'pagination'=> [
                    'total' => $aux->total(),
                    'current_page' => $aux->currentPage(),
                    'per_page' => $aux->perPage(),
                    'last_page'=> $aux->lastPage(),
                    'from'=> $aux->firstItem(),
                    'to'=>$aux->lastItem()
                ]

            ];

            return Response::json($data, 200);

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function show($id){
        try {
            $sede = Sedes::where('id', $id)->first();

            if (!$sede) {
                $data = [
                    'message' => 'Sede no encontrada',
                    'status' => 404,
                ];
                return Response::json($data, 404);
            }

            return Response::json($sede, 200);
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
                $data = [
                    'message' => 'Sede no encontrada',
                    'status' => 404,
                ];
                return Response::json($data, 404);
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
            $data = [
                'message' => 'Registro actualizado con éxito',
                'data' => $data
            ];

            return Response::json($data, 200);
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
                $data = [
                    'message' => 'Sede no encontrada',
                    'status' => 404,
                ];
                return Response::json($data, 404);
            }

            // Cambiar el estado entre true y false
            $sede->update(['estado' => !$sede->estado]);

            $data = [
                'message' => 'Sede Eliminada correctamente',
                'status' => 200,
            ];
            return Response::json($data, 200);
        } catch (Throwable $e) {
            throw $e;
        }
    }

}
