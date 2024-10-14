<?php

namespace App\Http\Controllers;

use App\FieldPrompt;
use Illuminate\Http\Request;

class FieldPromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = FieldPrompt::getTypes();
        $fields = FieldPrompt::getFields();
        $prompts = FieldPrompt::where('type', 'field_prompts')->get();

        return view('admin.prompt.index', [
            'types' => $types,
            'fields' => $fields,
            'prompts' => $prompts->toArray()
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

        if ($request->sortBy) {
            $sort = 'asc';
            if ($request->sortDesc) {
                $sort = 'desc';
            }

            $prompts->orderBy($request->sortBy, $sort);
        }

        return $prompts->paginate($request->perPage);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, int $id)
    {
        $prompt = FieldPrompt::find($id);

        if ($prompt) {
            $prompt->update($request->only('name', 'content'));
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
