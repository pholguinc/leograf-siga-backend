<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuStoreRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MenuController extends Controller
{
    use ResponseTrait;
    public function store(MenuStoreRequest $request)
    {

        echo $request->nombre . $request->id_modulo;
        return;
        try {
            DB::beginTransaction();

            $menus = new Menu();
            $menus->nombre = $request->nombre;
            $menus->id_modulo = $request->id_modulo;
            // $menus->estado = $request->estado;

            $codigoPrefix = '0001';

            $menus->codigo = $codigoPrefix;


            $menus->save();

            DB::commit();

            $data = new MenuResource($menus);

            return $this->responseJson($data);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function index(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $idModulo = $request->input('id_modulo');
        $nombreSede = $request->input('nombre');
        $estadoSede = $request->input('estado');

        $query = DB::select('SELECT * FROM listar_menus_grid_list(:offset, :limit, :id_modulo, :nombre, :estado);', [
            'offset' => $offset,
            'id_modulo' => $idModulo,
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
    }

    public function show($id)
    {

        try {
            $query = DB::select('SELECT * FROM listar_menus_por_id_list(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            $query = DB::select('SELECT * FROM cambiar_estado_menus(:id)', [':id' => $id]);

            if (empty($query)) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }


            return $this->responseJson($query);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
