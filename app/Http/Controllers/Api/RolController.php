<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\RolStoreRequest;
use App\Http\Requests\Roles\RolUpdateRequest;
use App\Http\Resources\RolesResource;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class RolController extends Controller
{
    public function index(Request $request){
        $query = DB::table('rol');

        // Filtrar por el parámetro rol
        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->where('rol', 'like', '%' . $rol . '%');
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
        $aux = RolesResource::collection($data);

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
        
    }
    public function store(RolStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $roles = new Rol();
            $roles->rol = $request->rol;
            $roles->estado = $request->estado;

            $codigoPrefix = '000';
            $roles->codigo = $codigoPrefix;

            $roles->save();

            $roles->codigo = $codigoPrefix . $roles->id;
            $roles->save();


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
            $sede = Rol::where('id', $id)->first();

            if (!$sede) {
                $data = [
                    'message' => 'rEGISTRO no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $messages = [
                'message' => $sede,
                'status' => 200
            ];

            return response()->json($messages);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function update(RolUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $roles = Rol::find($id);

            if (!$roles) {
                $data = [
                    'message' => 'Registro no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $roles->update([
                'rol' => $request->rol,
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

            $roles = Rol::find($id);

            if (!$roles) {
                $data = [
                    'message' => 'Registro no encontrada',
                    'status' => 404,
                ];
                return response()->json($data);
            }

            $statusRol = $roles->estado;

            if ($statusRol === 0) {
                $data = [
                    'message' => 'No exite',
                    'status' => 404
                ];
                return response()->json($data);
            }
            // TODO: Tu estatus por defecto es 1
            $roles->update(['estado' => 0]);
            $messages = [
                'message' => 'Rol Eliminada correctamente',
                'status' => 200,
            ];
            return response()->json($messages);
        } catch (Throwable $e) {
            throw $e;
        }
    }


}
