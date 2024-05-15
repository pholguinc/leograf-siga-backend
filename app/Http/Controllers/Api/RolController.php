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
    public function index(Request $request)
    {
        try {
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $nombreRol = $request->input('rol_nombre');
            $estadoRol = $request->input('estado');

            $query = DB::select('SELECT * FROM listar_roles_grid_list(:offset, :limit, :rol_nombre, :estado);', [
                'offset' => $offset,
                'limit' => $limit,
                'rol_nombre' => $nombreRol ? "%{$nombreRol}%" : null,
                'estado' => $estadoRol
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
            $query = DB::select('SELECT * FROM listar_roles_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $roles = Rol::find($id)->first();

            if (!$roles) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT roles_list_update(:id_rol, :nombre_rol)', [
                ':id_rol' => $id,
                ':nombre_rol' => $request->input('nombre_rol'),
            ]);

            DB::commit();
            return $this->responseJson($query);
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
