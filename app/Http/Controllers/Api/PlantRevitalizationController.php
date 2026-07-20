<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantRevitalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlantRevitalizationController extends Controller
{
    public function index()
    {
        $plants = PlantRevitalization::where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($plant) => $this->formatPlant($plant));

        return response()->json([
            'message' => 'Data revitalisasi tanaman berhasil diambil.',
            'data' => $plants,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = PlantRevitalization::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($plantQuery) use ($search) {
                $plantQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('scientific_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'published') {
            $query->where('is_published', true);
        }

        if ($request->status === 'draft') {
            $query->where('is_published', false);
        }

        $plants = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($plant) => $this->formatPlant($plant));

        return response()->json([
            'message' => 'Data revitalisasi tanaman admin berhasil diambil.',
            'data' => $plants,
            'meta' => [
                'total' => PlantRevitalization::count(),
                'published' => PlantRevitalization::where(
                    'is_published',
                    true
                )->count(),
                'draft' => PlantRevitalization::where(
                    'is_published',
                    false
                )->count(),
            ],
        ]);
    }

    public function show(PlantRevitalization $plantRevitalization)
    {
        if (!$plantRevitalization->is_published) {
            return response()->json([
                'message' => 'Data tanaman tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail revitalisasi tanaman berhasil diambil.',
            'data' => $this->formatPlant($plantRevitalization),
        ]);
    }

    public function adminShow(PlantRevitalization $plantRevitalization)
    {
        return response()->json([
            'message' => 'Detail revitalisasi tanaman admin berhasil diambil.',
            'data' => $this->formatPlant($plantRevitalization),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = $request->file('image')->store('plant-revitalizations', 'public');

        $plant = PlantRevitalization::create([
            'name' => $validated['name'],
            'scientific_name' => $validated['scientific_name'] ?? null,
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'is_published' => $validated['is_published'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $this->generateQrCode($plant);

        return response()->json([
            'message' => 'Revitalisasi tanaman berhasil ditambahkan.',
            'data' => $this->formatPlant($plant->fresh()),
        ], 201);
    }

    public function update(Request $request, PlantRevitalization $plantRevitalization)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            if ($plantRevitalization->image) {
                Storage::disk('public')->delete($plantRevitalization->image);
            }

            $validated['image'] = $request->file('image')->store('plant-revitalizations', 'public');
        }

        $plantRevitalization->update($validated);

        if (!$plantRevitalization->qr_code || !$plantRevitalization->qr_target_url) {
            $this->generateQrCode($plantRevitalization);
        }

        return response()->json([
            'message' => 'Revitalisasi tanaman berhasil diperbarui.',
            'data' => $this->formatPlant($plantRevitalization->fresh()),
        ]);
    }

    public function destroy(PlantRevitalization $plantRevitalization)
    {
        if ($plantRevitalization->image) {
            Storage::disk('public')->delete($plantRevitalization->image);
        }

        if ($plantRevitalization->qr_code) {
            Storage::disk('public')->delete($plantRevitalization->qr_code);
        }

        $plantRevitalization->delete();

        return response()->json([
            'message' => 'Revitalisasi tanaman berhasil dihapus.',
        ]);
    }

    public function regenerateQr(PlantRevitalization $plantRevitalization)
    {
        if ($plantRevitalization->qr_code) {
            Storage::disk('public')->delete($plantRevitalization->qr_code);
        }

        $this->generateQrCode($plantRevitalization);

        return response()->json([
            'message' => 'QR code revitalisasi tanaman berhasil dibuat ulang.',
            'data' => $this->formatPlant($plantRevitalization->fresh()),
        ]);
    }

    private function generateQrCode(PlantRevitalization $plant): void
    {
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
        $targetUrl = "{$frontendUrl}/revitalisasi-tanaman/{$plant->id}";

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($targetUrl);

        $qrPath = "qr/plant-revitalizations/plant-revitalization-{$plant->id}.svg";

        Storage::disk('public')->put($qrPath, $qrSvg);

        $plant->update([
            'qr_code' => $qrPath,
            'qr_target_url' => $targetUrl,
        ]);
    }

    private function formatPlant(PlantRevitalization $plant): array
    {
        return [
            'id' => $plant->id,
            'name' => $plant->name,
            'scientific_name' => $plant->scientific_name,
            'description' => $plant->description,

            'image' => $plant->image,
            'image_url' => $plant->image ? asset('storage/' . $plant->image) : null,

            'qr_code' => $plant->qr_code,
            'qr_code_url' => $plant->qr_code ? asset('storage/' . $plant->qr_code) : null,
            'qr_target_url' => $plant->qr_target_url,

            'is_published' => $plant->is_published,
            'sort_order' => $plant->sort_order,
            'created_at' => $plant->created_at,
            'updated_at' => $plant->updated_at,
        ];
    }
}