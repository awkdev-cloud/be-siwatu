<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
    public function index()
    {
        $categories = GalleryCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Data kategori galeri berhasil diambil.',
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:gallery_categories,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = GalleryCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Kategori galeri berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    public function show(GalleryCategory $galleryCategory)
    {
        return response()->json([
            'message' => 'Detail kategori galeri berhasil diambil.',
            'data' => $galleryCategory,
        ]);
    }

    public function update(Request $request, GalleryCategory $galleryCategory)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:gallery_categories,name,' . $galleryCategory->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $galleryCategory->update($validated);

        return response()->json([
            'message' => 'Kategori galeri berhasil diperbarui.',
            'data' => $galleryCategory,
        ]);
    }

    public function destroy(GalleryCategory $galleryCategory)
    {
        if ($galleryCategory->galleries()->exists()) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh data galeri.',
            ], 422);
        }

        $galleryCategory->delete();

        return response()->json([
            'message' => 'Kategori galeri berhasil dihapus.',
        ]);
    }
}