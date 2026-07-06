<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('category')
            ->where('is_published', true);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->category);
            });
        }

        $galleries = $query->latest()->get()->map(function ($gallery) {
            return $this->formatGallery($gallery);
        });

        return response()->json([
            'message' => 'Data galeri berhasil diambil.',
            'data' => $galleries,
        ]);
    }

    public function show(Gallery $gallery)
    {
        $gallery->load('category');

        return response()->json([
            'message' => 'Detail galeri berhasil diambil.',
            'data' => $this->formatGallery($gallery),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gallery_category_id' => ['required', 'exists:gallery_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $imagePath = $request->file('image')->store('galleries', 'public');

        $gallery = Gallery::create([
            'gallery_category_id' => $validated['gallery_category_id'],
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['title']),
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'is_published' => $validated['is_published'] ?? true,
        ]);

        $gallery->load('category');

        return response()->json([
            'message' => 'Galeri berhasil ditambahkan.',
            'data' => $this->formatGallery($gallery),
        ], 201);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'gallery_category_id' => ['sometimes', 'required', 'exists:gallery_categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['title']) && $validated['title'] !== $gallery->title) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $gallery->id);
        }

        if ($request->hasFile('image')) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }

            $validated['image'] = $request->file('image')->store('galleries', 'public');
        }

        $gallery->update($validated);
        $gallery->load('category');

        return response()->json([
            'message' => 'Galeri berhasil diperbarui.',
            'data' => $this->formatGallery($gallery),
        ]);
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return response()->json([
            'message' => 'Galeri berhasil dihapus.',
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Gallery::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function formatGallery(Gallery $gallery): array
    {
        return [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'slug' => $gallery->slug,
            'description' => $gallery->description,
            'image' => $gallery->image,
            'image_url' => asset('storage/' . $gallery->image),
            'is_published' => $gallery->is_published,
            'category' => [
                'id' => $gallery->category?->id,
                'name' => $gallery->category?->name,
                'slug' => $gallery->category?->slug,
            ],
            'created_at' => $gallery->created_at,
            'updated_at' => $gallery->updated_at,
        ];
    }
}