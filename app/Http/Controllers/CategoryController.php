<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /* ---------------- INDEX ---------------- */

    public function index()
    {
        $categories = Category::with('parent')
            ->latest()
            ->paginate(20);

        return view('pages.category.index', compact('categories'));
    }

    /* ---------------- CREATE ---------------- */

    public function create()
    {
        $parents = Category::whereNull('parent_id')->active()->get();

        return view('pages.category.create', compact('parents'));
    }

    /* ---------------- STORE ---------------- */

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, &$data) {
            $this->handleUploads($request, $data);
            Category::create($data);
        });

        return redirect()
            ->route('category.index')
            ->with('success', 'Category created successfully');
    }

    /* ---------------- EDIT ---------------- */

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->active()
            ->get();

        return view('pages.category.create', compact('category', 'parents'));
    }

    /* ---------------- UPDATE ---------------- */

    public function update(Request $request, Category $category)
    {
        $data = $this->validatedData($request, $category->id);

        DB::transaction(function () use ($request, $category, &$data) {
            $this->handleUploads($request, $data, $category);
            $category->update($data);
        });

        return redirect()
            ->route('category.index')
            ->with('success', 'Category updated successfully');
    }

    /* ---------------- DESTROY ---------------- */

    public function destroy(Category $category)
    {
        DB::transaction(function () use ($category) {

            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            if ($category->og_image) {
                Storage::disk('public')->delete($category->og_image);
            }

            $category->delete();
        });

        return redirect()
            ->route('category.index')
            ->with('success', 'Category deleted successfully');
    }

    /* =====================================================
       =============== PRIVATE HELPERS ======================
       ===================================================== */

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],

            'name' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                Rule::unique('categories', 'slug')->ignore($ignoreId),
            ],

            'level' => ['required', Rule::in(['main', 'sub', 'child'])],

            'sort_order' => ['required', 'integer', 'min:0'],

            'icon'  => ['nullable', 'image', 'max:1024'],
            'image' => ['nullable', 'image', 'max:2048'],

            // SEO
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'canonical_url'    => ['nullable', 'url'],
            'og_image'         => ['nullable', 'image', 'max:2048'],

            'is_active'    => ['nullable', 'boolean'],
            'is_indexable' => ['nullable', 'boolean'],
        ]);
    }

    private function handleUploads(Request $request, array &$data, ?Category $category = null): void
    {
        if ($request->hasFile('icon')) {
            if ($category?->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $data['icon'] = $request->file('icon')->store('categories/icons', 'public');
        }

        if ($request->hasFile('image')) {
            if ($category?->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories/images', 'public');
        }

        if ($request->hasFile('og_image')) {
            if ($category?->og_image) {
                Storage::disk('public')->delete($category->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('categories/seo', 'public');
        }

        $data['is_active']    = $request->boolean('is_active');
        $data['is_indexable'] = $request->boolean('is_indexable');
    }
}
