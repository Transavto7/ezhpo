<?php

namespace App\Http\Controllers;

use App\Actions\Terminal\Update\Dto\TerminalCheckUpdateAction;
use App\Actions\Terminal\Store\Dto\TerminalCheckStoreAction;
use App\Actions\Terminal\Store\Dto\TerminalDeviceStoreAction;
use App\Actions\Terminal\Store\TerminalCheckStoreHandler;
use App\Actions\Terminal\Store\TerminalDeviceStoreHandler;
use App\Actions\Terminal\Store\TerminalStoreHandler;
use App\Actions\Terminal\Update\TerminalCheckUpdateHandler;
use App\Actions\Terminal\Update\TerminalUpdateHandler;
use App\Anketa;
use App\Enums\DeviceEnum;
use App\FieldPrompt;
use App\Req;
use App\Role;
use App\TerminalCheck;
use App\TerminalDevice;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class TerminalController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
        $users = User::with(['roles', 'pv', 'company', 'pv.town', 'stamp', 'terminalDevices', 'terminalCheck'])
            ->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', 9);
            });

        if ($request->get('deleted')) {
            $users->with(['deleted_user'])->onlyTrashed();
        }
        if ($id = $request->get('hash_id')) {
            $users->whereIn('hash_id', $id);
        }

        if ($pv_id = $request->get('pv_id')) {
            $users->where('pv_id', $pv_id);
        }

        if ($sortBy = $request->get('sortBy', 'id')) {
            $users->orderBy($sortBy, $request->get('sortDesc') == 'true' ? 'DESC' : 'ASC');
        }

        $res = $users->paginate(100);
        if ($request->get('api')) {
            $terminals = $res->getCollection();
            $anketas = Anketa::whereIn('terminal_id', $terminals->pluck('id'))
                ->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())->select('created_at', 'terminal_id')->get();
            foreach ($terminals as $terminal) {
                $terminal->month_amount = $anketas->where('terminal_id', $terminal->id)
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())->count();

                $terminal->last_month_amount = $anketas->where('terminal_id', $terminal->id)
                    ->where('created_at', '<', Carbon::now()->startOfMonth())->count();
            }

            return response([
                'total_rows'   => $res->total(),
                'current_page' => $res->currentPage(),
                'items'        => $res->getCollection(),
            ]);
        }

        $fields = FieldPrompt::where('type', 'terminals')->get();

        $devicesOptions = collect(DeviceEnum::labels())
            ->map(function ($value, $key) {
                return [
                    'id' => $key,
                    'text' => $value
                ];
            })
            ->values();

        return view('admin.users_v2.terminal')
            ->with([
                'users' => $res,
                'fields' => $fields,
                'devicesOptions' => $devicesOptions
            ]);
    }

    public function getConnectionStatus(Request $request) {
        if (!$request->terminals_id) {
            return;
        }

        $terminals = User::whereIn('id', $request->terminals_id)->select('id', 'last_connection_at')->get();
        foreach ($terminals as $terminal) {
            $terminal->connected = false;
            if ($terminal->last_connection_at) {
                $terminal->connected = Carbon::now()->diffInSeconds($terminal->last_connection_at) < 20;
            }
        }

        return $terminals;
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $terminalStoreHandler = new TerminalStoreHandler();
            $terminalUpdateHandler = new TerminalUpdateHandler();
            $terminalCheckStoreHandler = new TerminalCheckStoreHandler();
            $terminalCheckUpdateHandler = new TerminalCheckUpdateHandler();
            $terminalDeviceStoreHandler = new TerminalDeviceStoreHandler();

            if ($request->input('user_id')) {
                $userId = $request->input('user_id');
                $terminalUpdateHandler->handle($request);
            }
            else {
                $userId = $terminalStoreHandler->handle($request);
            }

            if (TerminalCheck::where('user_id', '=', $userId)->get()->count()) {
                $terminalCheckUpdateHandler->handle(
                    new TerminalCheckUpdateAction(
                        $userId,
                        $request->input('serial_number'),
                        Carbon::parse($request->input('date_check'))
                    )
                );
            }
            else {
                $terminalCheckStoreHandler->handle(new TerminalCheckStoreAction(
                    $userId,
                    $request->input('serial_number'),
                    Carbon::parse($request->input('date_check'))
                ));
            }

            // todo(hv): вынести в action
            TerminalDevice::where('user_id', '=', $userId)->forceDelete();
            foreach ($request->input('devices') as  $device) {
                $terminalDeviceStoreHandler->handle(new TerminalDeviceStoreAction(
                    $userId,
                    $device['id'],
                    $device['serial_number']
                ));
            }

            $user = User::find($userId);
            $user->roles()->sync([9]);

            DB::commit();

            return response([
                'status'    => true,
                'user_info' => User::with(['company'])
                    ->find($user->id),
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response([
                'status' => 422,
                'errors' => [$exception->getMessage()]
            ], 422);
        }
    }
}
