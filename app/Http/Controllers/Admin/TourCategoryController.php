<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourCategoryController
{
    public function index(Request $request): View
    {
        $query = TourCategory::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        if ($request->filled('status')) {
            if ($request->string('status')->toString() === 'active') {
                $query->where('is_active', true);
            }

            if ($request->string('status')->toString() === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tour-categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.tour-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'] ?? '';
        $slug = trim($slug) !== '' ? Str::slug($slug) : Str::slug($validated['name']);

        $category = new TourCategory();
        $category->name = $validated['name'];
        $category->slug = $slug;
        $category->sort_order = (int) ($validated['sort_order'] ?? 0);
        $category->is_active = (bool) ($validated['is_active'] ?? true);
        $category->save();

        return redirect()->route('admin.tour-categories.index')->with('status', 'Kategori trip berhasil ditambahkan.');
    }

    public function edit(TourCategory $tourCategory): View
    {
        return view('admin.tour-categories.edit', [
            'category' => $tourCategory,
        ]);
    }

    public function update(Request $request, TourCategory $tourCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'] ?? '';
        $slug = trim($slug) !== '' ? Str::slug($slug) : Str::slug($validated['name']);

        $tourCategory->name = $validated['name'];
        $tourCategory->slug = $slug;
        $tourCategory->sort_order = (int) ($validated['sort_order'] ?? 0);
        $tourCategory->is_active = (bool) ($validated['is_active'] ?? false);
        $tourCategory->save();

        return redirect()->route('admin.tour-categories.index')->with('status', 'Kategori trip berhasil diperbarui.');
    }

    public function destroy(TourCategory $tourCategory): RedirectResponse
    {
        $tourCategory->delete();

        return redirect()->route('admin.tour-categories.index')->with('status', 'Kategori trip berhasil dihapus.');
    }
}
