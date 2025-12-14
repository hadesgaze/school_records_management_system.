<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Http\Request;

class CategoryFieldController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Category $category)
    {
        $fieldTypes = [
            'text' => 'Text',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'email' => 'Email',
            'date' => 'Date',
            'datetime' => 'Date Time',
            'select' => 'Dropdown Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button',
            'file' => 'File Upload',
        ];

        return view('admin.category-fields.create', compact('category', 'fieldTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'field_description' => 'nullable|string',
            'options' => 'nullable|string',
            'field_order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $field = CategoryField::create([
            'name' => $request->field_name,
            'type' => $request->field_type,
            'description' => $request->field_description,
            'options' => $request->options,
            'order' => $request->field_order ?? 0,
            'is_required' => $request->has('is_required') ? true : false,
            'category_id' => $request->category_id,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Field created successfully.',
                'field' => $field
            ]);
        }

        return redirect()->back()->with('success', 'Field created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryField $categoryField)
    {
        $category = $categoryField->category;
        $fieldTypes = [
            'text' => 'Text',
            'textarea' => 'Text Area',
            'number' => 'Number',
            'email' => 'Email',
            'date' => 'Date',
            'datetime' => 'Date Time',
            'select' => 'Dropdown Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button',
            'file' => 'File Upload',
        ];

        return view('admin.category-fields.edit', compact('categoryField', 'category', 'fieldTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $field = CategoryField::findOrFail($id);

        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string',
            'field_description' => 'nullable|string',
            'options' => 'nullable|string',
            'field_order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $field->update([
            'name' => $request->field_name,
            'type' => $request->field_type,
            'description' => $request->field_description,
            'options' => $request->options,
            'order' => $request->field_order ?? 0,
            'is_required' => $request->has('is_required') ? true : false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Field updated successfully.',
                'field' => $field
            ]);
        }

        return redirect()->back()->with('success', 'Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $field = CategoryField::findOrFail($id);
        $field->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Field deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Field deleted successfully.');
    }
}