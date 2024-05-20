<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
* @OA\Info(
*             title="Proyecto API Leograf-Siga",
*             version="1.0",
* )
*
* @OA\Server(url="http://leograf-siga-backend.test")
*/

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

}
