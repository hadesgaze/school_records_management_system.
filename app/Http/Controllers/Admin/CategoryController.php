<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $categories = Category::withCount('fields')->latest()->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = [
            'administrator' => 'Administrator',
            'dean' => 'Dean',
            'chairperson' => 'Chairperson',
            'faculty' => 'Faculty',
        ];

        return view('admin.categories-create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'accessible_roles' => 'required|array|min:1',
            'accessible_roles.*' => 'string',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required_with:fields|string|max:255',
            'fields.*.type' => 'required_with:fields|string',
            'fields.*.description' => 'nullable|string',
            'fields.*.options' => 'nullable|string',
            'fields.*.order' => 'nullable|integer|min:0',
            'fields.*.is_required' => 'nullable|boolean',
        ]);

        // Create the category
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'accessible_roles' => $request->accessible_roles,
        ]);

        // Create fields if provided
if ($request->has('fields') && is_array($request->fields)) {
    foreach ($request->fields as $fieldData) {
        CategoryField::create([
            'category_id' => $category->id,
            'name' => $fieldData['name'],
            'type' => $fieldData['type'],
            'description' => $fieldData['description'] ?? null,
            'options' => $fieldData['options'] ?? null,
            'order' => $fieldData['order'] ?? 0,
            'is_required' => isset($fieldData['is_required']) ? true : false,
        ]);
    }
}


        $fieldCount = $request->has('fields') ? count($request->fields) : 0;
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' created successfully with {$fieldCount} field(s).");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('fields');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $roles = [
            'administrator' => 'Administrator',
            'dean' => 'Dean',
            'chairperson' => 'Chairperson',
            'faculty' => 'Faculty',
        ];

        $category->load('fields');
        
        return view('admin.categories-edit', compact('category', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'accessible_roles' => 'required|array|min:1',
            'accessible_roles.*' => 'string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'accessible_roles' => $request->accessible_roles,
        ]);

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $fieldCount = $category->fields()->count();
        
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Category '{$categoryName}' and its {$fieldCount} field(s) deleted successfully.");
    }
}