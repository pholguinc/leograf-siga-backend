<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modulos\ModulosStoreRequest;
use App\Http\Requests\Modulos\ModulosUpdateRequest;
use App\Http\Resources\ModulosResource;
use App\Models\Modulo;
use App\Models\Modulos;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
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
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $codigoPrefix = 'MO';
            $nombreModulo = $request->input('nombre_modulo');
            $image_url = $request->input('image_url');
            $statement = DB::connection()->getPdo()->prepare('SELECT last_value FROM modulos_id_seq');
            $statement->execute();
            $idModulo = $statement->fetchColumn();

            $codigoModulo = $codigoPrefix . $idModulo;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM modulos_list_create(:id_modulo,:codigo,:nombre,:alias, :image_url)');
            $query->bindParam(':id_modulo', $idModulo);
            $query->bindParam(':nombre', $nombreModulo);
            $query->bindParam(':codigo', $codigoModulo);
            $query->bindParam(':alias', $codigoPrefix);
            $query->bindParam(':image_url', $image_url);

            $sede = new Modulo();

            $query->execute();
            $moduloData = $query->fetch(PDO::FETCH_ASSOC);

            DB::commit();


            return $this->responseJson($moduloData);
        } catch (Throwable $e) {
            DB::rollBack();
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

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $modulos = Modulo::find($id)->first();

            if (!$modulos) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT modulos_list_update(:id_modulo, :nombre_modulo, :image_url)', [
                ':id_modulo' => $id,
                ':nombre_modulo' => $request->input('nombre_modulo'),
                ':image_url' => $request->input('image_url'),
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
