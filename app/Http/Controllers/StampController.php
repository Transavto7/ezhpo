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
    public function getAll(Request $request) {
        $prompts = Stamp::query();

        if ($request->trash) {
            $prompts->with(['deleted_user'])->onlyTrashed();
        }

        if ($request->company_name) {
            $prompts->like('company_name', '%' . $request->company_name . '%');
        }

        if ($request->licence) {
            $prompts->like('licence', '%' . $request->field . '%');
        }

        if ($request->sortBy) {
            $sort = 'asc';
            if ($request->sortDesc) {
                $sort = 'desc';
            }

            $prompts->orderBy($request->sortBy, $sort);
        }

        return $prompts->paginate(15);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Stamp::create($request->only('name', 'company_name', 'licence'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Stamp $stamp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stamp $stamp)
    {
        //
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
