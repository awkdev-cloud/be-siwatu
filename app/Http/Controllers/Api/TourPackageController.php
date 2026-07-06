<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = TourPackage::where('is_active', true);

        if ($request->filled('featured')) {
            $query->where('is_featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
        }

        $packages = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($package) {
                return $this->formatPackage($package);
            });

        return response()->json([
            'message' => 'Data paket wisata berhasil diambil.',
            'data' => $packages,
        ]);
    }

    public function adminIndex()
    {
        $packages = TourPackage::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($package) {
                return $this->formatPackage($package);
            });

        return response()->json([
            'message' => 'Data paket wisata admin berhasil diambil.',
            'data' => $packages,
        ]);
    }

    public function show(TourPackage $tourPackage)
    {
        if (!$tourPackage->is_active) {
            return response()->json([
                'message' => 'Paket wisata tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail paket wisata berhasil diambil.',
            'data' => $this->formatPackage($tourPackage),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'package_type' => ['required', 'string', 'max:255'],
            'highlight_label' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $package = TourPackage::create([
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['title']),
            'package_type' => $validated['package_type'],
            'highlight_label' => $validated['highlight_label'] ?? null,
            'description' => $validated['description'],
            'facilities' => $validated['facilities'] ?? [],
            'duration' => $validated['duration'] ?? null,
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Paket wisata berhasil ditambahkan.',
            'data' => $this->formatPackage($package),
        ], 201);
    }

    public function update(Request $request, TourPackage $tourPackage)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'package_type' => ['sometimes', 'required', 'string', 'max:255'],
            'highlight_label' => ['nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($validated['title']) && $validated['title'] !== $tourPackage->title) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $tourPackage->id);
        }

        $tourPackage->update($validated);

        return response()->json([
            'message' => 'Paket wisata berhasil diperbarui.',
            'data' => $this->formatPackage($tourPackage),
        ]);
    }

    public function destroy(TourPackage $tourPackage)
    {
        $tourPackage->delete();

        return response()->json([
            'message' => 'Paket wisata berhasil dihapus.',
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            TourPackage::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function formatPackage(TourPackage $package): array
    {
        return [
            'id' => $package->id,
            'title' => $package->title,
            'slug' => $package->slug,
            'package_type' => $package->package_type,
            'package_type_uppercase' => strtoupper($package->package_type),
            'highlight_label' => $package->highlight_label,
            'description' => $package->description,
            'facilities' => $package->facilities ?? [],
            'duration' => $package->duration,
            'price' => $package->price,
            'price_formatted' => 'Rp' . number_format($package->price, 0, ',', '.'),
            'price_label' => 'Mulai dari',
            'is_active' => $package->is_active,
            'is_featured' => $package->is_featured,
            'sort_order' => $package->sort_order,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at,
        ];
    }
}