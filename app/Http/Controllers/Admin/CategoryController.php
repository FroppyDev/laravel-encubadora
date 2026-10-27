<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'edit' => ['nullable', 'integer', 'exists:categories,id'],
        ]);
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? '';
        $categories = Category::withCount('projects')
            ->when($search !== '', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']))
            ->when($status !== '', fn ($q) => $q->where('is_active', $status === 'active'))
            ->orderBy('name')->paginate(10)->withQueryString();
        $editing = isset($filters['edit']) ? Category::findOrFail($filters['edit']) : null;
        $total = Category::count();
        $active = Category::where('is_active', true)->count();
        $projects = Project::count();

        return view('admin.categories', compact('categories', 'editing', 'search', 'status', 'total', 'active', 'projects'));
    }

    private function data(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ], ['slug.unique' => 'Este slug ya está en uso.', 'slug.regex' => 'Usa letras minúsculas, números y guiones en el slug.']);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin($request);
        Category::create($this->data($request));

        return redirect()->route('admin.categories.index')->with('status_message', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeAdmin($request);
        $category->update($this->data($request, $category));

        return redirect()->route('admin.categories.index')->with('status_message', 'Categoría actualizada correctamente.');
    }

    public function status(Request $request, Category $category)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status_message', $category->is_active ? 'Categoría reactivada.' : 'Categoría desactivada. Sus proyectos se conservan.');
    }
}
