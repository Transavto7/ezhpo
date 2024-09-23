<?php

namespace App\Http\Controllers;

use App\FieldPrompt;
use App\Stamp;
use Illuminate\Http\Request;

class StampController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = FieldPrompt::where('type', 'stamps')->get();
        return view('admin.stamp.index', [
            'fields' => $fields
        ]);
    }

    /*
     * Axios get all rows in table
     */
    public function getAll(Request $request)
    {
        $stamps = Stamp::query();

        if ($request->trash) {
            $stamps->with(['deleted_user'])->onlyTrashed();
        }

        if ($request->company_name) {
            $stamps->where('company_name', 'like', '%' . $request->company_name . '%');
        }

        if ($request->licence) {
            $stamps->where('licence', 'like', '%' . $request->licence . '%');
        }

        if ($request->sortBy) {
            $sort = 'asc';
            if ($request->sortDesc) {
                $sort = 'desc';
            }

            $stamps->orderBy($request->sortBy, $sort);
        }

        return $stamps->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Stamp::create($request->only('name', 'company_name', 'licence'));
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, int $id)
    {
        $stamp = Stamp::find($id);

        if ($stamp) {
            $stamp->update($request->only('name', 'company_name', 'licence'));
        }
    }

    /**
     * List stamp by find string
     */
    public function find(Request $request)
    {
        $query = $request->search ?? '';

        return Stamp::query()->where('name', 'like', '%' . $query . '%')
            ->limit(100)->get();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $stamp = Stamp::withTrashed()->find($id);
        if ($stamp->trashed()) {
            $stamp->restore();
        } else {
            $stamp->delete();
        }
    }
}
