<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\RolStoreRequest;
use App\Http\Requests\Roles\RolUpdateRequest;
use App\Http\Resources\RolesResource;
use App\Models\Rol;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class RolController extends Controller
{
    use ResponseTrait;
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
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $codigoPrefix = 'RO0';
            $nombreRol = $request->input('nombre_rol');
            $statement = DB::connection()->getPdo()->prepare('SELECT last_value FROM roles_id_seq');
            $statement->execute();
            $idRol = $statement->fetchColumn();

            $codigoRol = $codigoPrefix . $idRol;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM roles_list_create(:id_rol,:nombre,:codigo)');
            $query->bindParam(':id_rol', $idRol);
            $query->bindParam(':nombre', $nombreRol);
            $query->bindParam(':codigo', $codigoRol);


            $rol = new Rol();

            $query->execute();
            $sedeData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();


            return $this->responseJson($sedeData);
        } catch (Throwable $e) {
            DB::rollBack();
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
