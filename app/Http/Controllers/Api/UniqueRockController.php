<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniqueRock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UniqueRockController extends Controller
{
    public function index()
    {
        $rocks = UniqueRock::where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($rock) => $this->formatRock($rock));

        return response()->json([
            'message' => 'Data bebatuan unik berhasil diambil.',
            'data' => $rocks,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = UniqueRock::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($rockQuery) use ($search) {
                $rockQuery
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

        $rocks = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($rock) => $this->formatRock($rock));

        return response()->json([
            'message' => 'Data bebatuan unik admin berhasil diambil.',
            'data' => $rocks,
            'meta' => [
                'total' => UniqueRock::count(),
                'published' => UniqueRock::where(
                    'is_published',
                    true
                )->count(),
                'draft' => UniqueRock::where(
                    'is_published',
                    false
                )->count(),
            ],
        ]);
    }

    public function show(UniqueRock $uniqueRock)
    {
        if (!$uniqueRock->is_published) {
            return response()->json([
                'message' => 'Bebatuan unik tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail bebatuan unik berhasil diambil.',
            'data' => $this->formatRock($uniqueRock),
        ]);
    }

    public function adminShow(UniqueRock $uniqueRock)
    {
        return response()->json([
            'message' => 'Detail bebatuan unik admin berhasil diambil.',
            'data' => $this->formatRock($uniqueRock),
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

        $imagePath = $request->file('image')->store('unique-rocks', 'public');

        $rock = UniqueRock::create([
            'name' => $validated['name'],
            'scientific_name' => $validated['scientific_name'] ?? null,
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'is_published' => $validated['is_published'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $this->generateQrCode($rock);

        return response()->json([
            'message' => 'Bebatuan unik berhasil ditambahkan.',
            'data' => $this->formatRock($rock->fresh()),
        ], 201);
    }

    public function update(Request $request, UniqueRock $uniqueRock)
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
            if ($uniqueRock->image) {
                Storage::disk('public')->delete($uniqueRock->image);
            }

            $validated['image'] = $request->file('image')->store('unique-rocks', 'public');
        }

        $uniqueRock->update($validated);

        if (!$uniqueRock->qr_code || !$uniqueRock->qr_target_url) {
            $this->generateQrCode($uniqueRock);
        }

        return response()->json([
            'message' => 'Bebatuan unik berhasil diperbarui.',
            'data' => $this->formatRock($uniqueRock->fresh()),
        ]);
    }

    public function destroy(UniqueRock $uniqueRock)
    {
        if ($uniqueRock->image) {
            Storage::disk('public')->delete($uniqueRock->image);
        }

        if ($uniqueRock->qr_code) {
            Storage::disk('public')->delete($uniqueRock->qr_code);
        }

        $uniqueRock->delete();

        return response()->json([
            'message' => 'Bebatuan unik berhasil dihapus.',
        ]);
    }

    public function regenerateQr(UniqueRock $uniqueRock)
    {
        if ($uniqueRock->qr_code) {
            Storage::disk('public')->delete($uniqueRock->qr_code);
        }

        $this->generateQrCode($uniqueRock);

        return response()->json([
            'message' => 'QR code bebatuan unik berhasil dibuat ulang.',
            'data' => $this->formatRock($uniqueRock->fresh()),
        ]);
    }

    private function generateQrCode(UniqueRock $rock): void
    {
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://alaswatukebonan.my.id')), '/');
        $targetUrl = "{$frontendUrl}/bebatuan-unik/{$rock->id}";

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($targetUrl);

        $qrPath = "qr/unique-rocks/unique-rock-{$rock->id}.svg";

        Storage::disk('public')->put($qrPath, $qrSvg);

        $rock->update([
            'qr_code' => $qrPath,
            'qr_target_url' => $targetUrl,
        ]);
    }

    private function formatRock(UniqueRock $rock): array
    {
        return [
            'id' => $rock->id,
            'name' => $rock->name,
            'scientific_name' => $rock->scientific_name,
            'description' => $rock->description,

            'image' => $rock->image,
            'image_url' => $rock->image ? asset('storage/' . $rock->image) : null,

            'qr_code' => $rock->qr_code,
            'qr_code_url' => $rock->qr_code ? asset('storage/' . $rock->qr_code) : null,
            'qr_target_url' => $rock->qr_target_url,

            'is_published' => $rock->is_published,
            'sort_order' => $rock->sort_order,
            'created_at' => $rock->created_at,
            'updated_at' => $rock->updated_at,
        ];
    }
}