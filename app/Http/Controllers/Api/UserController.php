<?php

namespace App\Http\Controllers\Api;

use App\Services\PersService;
use App\Services\SpoCoreService;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        $spo = new PersService(new SpoCoreService());
        return [
            'user' => \Spo::user()->getFio(),
            'changePasswordLink' => route('change_password'),
            'logoutLink' => route('logout'),
        ];
    }
}
