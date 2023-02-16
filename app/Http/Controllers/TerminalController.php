<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\FieldPrompt;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TerminalController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
        $users = User::with(['roles', 'pv', 'company', 'pv.town'])
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

        $res = $users->paginate();
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

        return view('admin.users_v2.terminal')
            ->with([
                'users' => $res,
                'fields' => $fields
            ]);
    }

    public function update(Request $request)
    {
        if ($userId = $request->get('user_id')) {
            $user           = User::find($userId);
            $user->name     = $request->get('name', null);
            $user->timezone = $request->get('timezone', null);
            $user->company_id = $request->get('company_id', null);
            $user->blocked  = $request->get('blocked', 0);
            $user->pv_id = $request->get('pv', null);
            $user->save();
        } else {
            $api_token = Hash::make(date('H:i:s'));
            $user = User::create([
                'name'     => $request->get('name', null),
                'hash_id'  => mt_rand(1000, 9999).date('s'),
                'timezone' => $request->get('timezone', null),
                'company_id' => $request->get('company_id', null),
                'blocked'  => $request->get('blocked', 0),
                'password' => $api_token,
                'api_token' => $api_token,
                'pv_id' => $request->get('pv', null),
            ]);
        }

        $user->roles()->sync([9]);

        return response([
            'status'    => true,
            'user_info' => User::with(['company'])
                ->find($user->id),
        ]);
    }
}