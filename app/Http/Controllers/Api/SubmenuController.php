<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submenu;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class SubmenuController extends Controller
{
    use ResponseTrait;


    public function index(Request $request)
    {
        try {
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $nombreSubmenu = $request->input('submenu_nombre');
            $idModulo = $request->input('id_modulo');
            $idMenu = $request->input('id_menu');
            $estadoSubmenu = $request->input('estado');

            $query = DB::select('SELECT * FROM listar_submenus_grid_list(:offset, :limit, :id_modulo, :id_menu, :submenu_nombre, :estado);', [
                'offset' => $offset,
                'limit' => $limit,
                'id_modulo'=> $idModulo,
                'id_menu' => $idMenu,
                'submenu_nombre' => $nombreSubmenu,
                'estado' => $estadoSubmenu
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

            $codigoPrefix = 'SU0';
            $nombreSubMenu = $request->input('nombre_submenu');
            $menuId = $request->input('id_menu');
            $moduloId = $request->input('id_modulo');
            $statement = DB::connection()->getPdo()->prepare('SELECT last_value FROM submenus_id_seq');
            $statement->execute();
            $idSubMenu = $statement->fetchColumn();

            $moduloAliasQuery = DB::table('modulos')->where('id', $moduloId)->select('alias')->first();
            $moduloAlias = $moduloAliasQuery ? $moduloAliasQuery->alias : '';
            
            $menuAliasQuery = DB::table('menus')->where('id', $menuId)->select('alias')->first();
            $menuAlias = $menuAliasQuery ? $menuAliasQuery->alias : '';


            $codigoSubMenu = $moduloAlias . $moduloId . $menuAlias . $menuId. $codigoPrefix . $idSubMenu;


            $query = DB::connection()->getPdo()->prepare('SELECT * FROM submenus_list_create(:id_submenu,:codigo,:nombre, :id_menu, :id_modulo)');
            $query->bindParam(':id_submenu', $idSubMenu);
            $query->bindParam(':codigo', $codigoSubMenu);
            $query->bindParam(':nombre', $nombreSubMenu);
            $query->bindParam(':id_menu', $menuId);
            $query->bindParam(':id_modulo', $moduloId);

            $submenu = new Submenu();

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
            $query = DB::select('SELECT * FROM listar_submenus_por_id_list(:id)', [':id' => $id]);

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
            $query = DB::select('SELECT * FROM cambiar_estado_submenus(:id)', [':id' => $id]);

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

            $submenus = Submenu::find($id)->first();

            if (!$submenus) {
                return $this->responseErrorJson('El registro no fue encontrado');
            }

            $query = DB::select('SELECT submenus_list_update(:id_submenu, :nombre_submenu, :id_menu, :id_modulo)', [
                ':id_submenu' => $id,
                ':nombre_submenu' => $request->input('nombre_submenu'),
                ':id_menu' => $request->input('id_menu'),
                ':id_modulo' => $request->input('id_modulo'),
            ]);

            DB::commit();
            return $this->responseJson($query);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }




}
