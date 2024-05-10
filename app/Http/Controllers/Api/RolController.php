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

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $nombreMenu = $request->input('nombre');
        $estadoMenu = $request->input('estado'); 
        
        $query = DB::select('SELECT * FROM listar_menus_grid_list(:offset, :limit, :nombre, :estado);', [
            'offset' => $offset,
            'limit' => $limit,
            'nombre' => $nombreMenu,
            'estado' => $estadoMenu
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
            $query = DB::select('SELECT * FROM cambiar_estado_roles(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }


}
