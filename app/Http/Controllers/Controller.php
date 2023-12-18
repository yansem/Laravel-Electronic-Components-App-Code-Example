<?php

namespace App\Http\Controllers;

use App\Models\PermissionSpo;
use App\Models\UserSpo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected int $userId;
    protected string $userPermission;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = \Spo::user()->id;
            $permId = \Spo::user()->permissionsSpo[config('app.spo_base')];
            $this->userPermission = (new PermissionSpo())->getSlug($permId);
            return $next($request);
        });
    }
}
