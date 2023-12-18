<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\HistoryResource;
use App\Http\Resources\HistorySelectDataResource;
use App\Models\History;
use App\Models\LogCodes;
use App\Models\Operation;
use App\Models\UserSpo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $items = History::query()
            ->join('log_codes', 'log_codes.id', '=', 'histories.log_code_id')
            ->join('operations', 'operations.id', '=', 'histories.operation_id')
            ->select(
                'histories.*',
                'log_codes.title as log_code_title',
                'operations.title as operation_title'
            )
            ->with('historyable')
            ->withFilters($request->all())
            ->orderBy('created_at', 'desc')
            ->paginate();
        return HistoryResource::collection($items);
    }

    public function getSelectData()
    {
        $selectData['entities'] = LogCodes::orderBy('title', 'asc')->get();
        $selectData['operations'] = Operation::orderBy('title', 'asc')->get();
        $selectData['users'] = History::distinct()->get(['user_id'])->each(function ($user) {
            if ($user->user_id) {
                $user->user_name = \Spo::user(\Spo::getUserInfoById($user->user_id))->getFio();
            } else {
                $user->user_id = UserSpo::SCHEDULED_SYNCHRONIZATION_ID;
                $user->user_name = __('scheduled synchronization');
            }
        });

        return new HistorySelectDataResource($selectData);
    }
}
