<?php

namespace App\Http\Controllers\Api;

use App\Models\Logs;
use App\Http\Resources\LogsCollection;
use Illuminate\Routing\Controller;

class LogController extends Controller
{
    public function index()
    {
        $items = Logs::with('code')->orderByDesc('id')->paginate(Logs::PER_PAGE);
        return new LogsCollection($items);
    }
}
