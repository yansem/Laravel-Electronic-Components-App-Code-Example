<?php

namespace App\Http\Controllers;

use App\Services\PersService;
use App\Services\SpoCoreService;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $spo = new PersService(new SpoCoreService());
        return redirect($spo->getUrlHost().'/general/change_password.php');

    }
}
