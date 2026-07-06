<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index()
    {
        $socialLinks = SocialLink::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'message' => 'Data media sosial berhasil diambil.',
            'data' => $socialLinks,
        ]);
    }

    public function adminIndex()
    {
        $socialLinks = SocialLink::orderBy('sort_order')->get();

        return response()->json([
            'message' => 'Data media sosial admin berhasil diambil.',
            'data' => $socialLinks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $socialLink = SocialLink::create([
            'platform' => $validated['platform'],
            'username' => $validated['username'] ?? null,
            'url' => $validated['url'],
            'icon' => $validated['icon'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Media sosial berhasil ditambahkan.',
            'data' => $socialLink,
        ], 201);
    }

    public function show(SocialLink $socialLink)
    {
        return response()->json([
            'message' => 'Detail media sosial berhasil diambil.',
            'data' => $socialLink,
        ]);
    }

    public function update(Request $request, SocialLink $socialLink)
    {
        $validated = $request->validate([
            'platform' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'url' => ['sometimes', 'required', 'url'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $socialLink->update($validated);

        return response()->json([
            'message' => 'Media sosial berhasil diperbarui.',
            'data' => $socialLink,
        ]);
    }

    public function destroy(SocialLink $socialLink)
    {
        $socialLink->delete();

        return response()->json([
            'message' => 'Media sosial berhasil dihapus.',
        ]);
    }
}