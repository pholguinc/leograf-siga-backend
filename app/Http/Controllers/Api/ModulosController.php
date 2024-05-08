<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modulos\ModulosStoreRequest;
use App\Http\Requests\Modulos\ModulosUpdateRequest;
use App\Http\Resources\ModulosResource;
use App\Models\Modulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ModulosController extends Controller
{

    public function index(Request $request)
    {
        try {

            $query = DB::table('modulos');

            // Filtrar por el parámetro nombre
            if ($request->filled('nombre')) {
                $nombre = $request->input('nombre');
                $query->where('nombre', 'like', '%' . $nombre . '%');
            }

            if ($request->filled('codigo')) {
                $codigo = $request->input('codigo');
                $query->where('codigo', 'like', '%' . $codigo . '%');
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
            $aux = ModulosResource::collection($data);

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

            $modulo->codigo = $codigoPrefix . $modulo->id;
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
            $modulo = Modulos::where('id', $id)->first();

            if (!$modulo) {
                $data = [
                    'message' => 'rEGISTRO no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $messages = [
                'message' => $modulo,
                'status' => 200
            ];

            return response()->json($messages);
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

            $modulo = Modulos::find($id);

            if (!$modulo) {
                $data = [
                    'message' => 'Registro no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $statusRol = $modulo->estado;

            if ($statusRol === 0) {
                $data = [
                    'message' => 'No exite',
                    'status' => 404
                ];
                return response()->json($data);
            }
            // TODO: Tu estatus por defecto es 1
            $modulo->update(['estado' => 0]);
            $messages = [
                'message' => 'Modulo Eliminada correctamente',
                'status' => 200,
            ];
            return response()->json($messages);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
