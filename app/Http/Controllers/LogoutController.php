<?php
namespace App\Http\Controllers;

use App\Services\PersService;
use App\Services\SpoCoreService;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $spo = new PersService(new SpoCoreService());
        return redirect($spo->getUrlHost().'/authorization?logout=1');
    }
}
