<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SedesResource;
use App\Models\Sedes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SedesController extends Controller
{
    public function store(Request $request)
    {

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'nombre' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('sedes')->where(function ($query) use ($request) {
                        return $query->where('nombre', $request->nombre);
                    }),
                ],
                'estado' => 'required|boolean'
            ]);
            if ($validator->fails()) {
                $data = [
                    'message' => 'Hubo un error en las validaciones',
                    'errors' => $validator->errors(),
                    'status' => 400
                ];

                return response()->json($data, 400);
            }

            $sedes = DB::table('sedes')->insert([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
            ]);

            if ($sedes) {
                DB::commit();

                $data = [
                    'message' => 'Registro exitoso',
                    'status' => 200
                ];
                return response()->json($data, 200);
            } else {
                $data = [
                    'message' => 'Hubo un problema al intentar registrar',
                    'status' => 500
                ];
                return response()->json($data, 500);
            }
        } catch (\Throwable $e) {
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

            return response()->json($data);

        } catch (\Throwable $e) {
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
                return response()->json($data, 404);
            }

            return response()->json($sede, 200);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $sedes = Sedes::find($id);

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
            return response()->json([
                'message' => 'Registro actualizado con éxito',
                'data' => $data
            ]);
        } catch (\Throwable $e) {
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
                'message' => 'El estado se actualizó con éxito',
                'status' => 200,
                'data' => $sede
            ];
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

}
