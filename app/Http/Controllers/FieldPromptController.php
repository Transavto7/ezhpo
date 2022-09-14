<?php

namespace App\Http\Controllers;

use App\FieldPrompt;
use App\Req;
use Illuminate\Http\Request;

class FieldPromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = FieldPrompt::getTypes();
        $fields = FieldPrompt::getFieldList();

        return view('admin.prompt.index', [
            'types' => $types,
            'fields' => $fields
        ]);
    }

    /*
     * Axios get all rows in table
     */
    public function getAll(Request $request) {
        $prompts = FieldPrompt::query();

        if ($request->trash) {
            $prompts->with(['deleted_user'])->onlyTrashed();
        }

        if ($request->type) {
            $prompts->where('type', $request->type);
        }

        if ($request->field) {
            $prompts->where('field', $request->field);
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
        FieldPrompt::updateOrCreate(
            [
            'type' => $request->type,
            'field' => $request->field,
            ],
            [
            'type' => $request->type,
            'field' => $request->field,
            'content' => $request->get('content')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(FieldPrompt $fieldPrompt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FieldPrompt $fieldPrompt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, int $id)
    {
        $prompt = FieldPrompt::find($id);

        if ($prompt) {
            $prompt->update($request->only('type', 'field', 'content'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $field = FieldPrompt::withTrashed()->find($id);
        if ($field->trashed()) {
            $field->restore();
        } else {
            $field->delete();
        }
    }
}
