<?php

namespace App\Http\Controllers\Api;

use App\Services\Program\CoreService;
use Illuminate\Routing\Controller;

class MenuController extends Controller
{
    public function index()
    {
        return \Menu::getMenu();
    }
}
