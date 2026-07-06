<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);

        $reviews = Review::where('is_published', true)
            ->orderByDesc('review_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($review) {
                return $this->formatReview($review);
            });

        return response()->json([
            'message' => 'Data review berhasil diambil.',
            'data' => $reviews,
        ]);
    }

    public function adminIndex()
    {
        $reviews = Review::orderByDesc('review_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($review) {
                return $this->formatReview($review);
            });

        return response()->json([
            'message' => 'Data review admin berhasil diambil.',
            'data' => $reviews,
        ]);
    }

    public function show(Review $review)
    {
        if (!$review->is_published) {
            return response()->json([
                'message' => 'Review tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail review berhasil diambil.',
            'data' => $this->formatReview($review),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
            'review_date' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $review = Review::create([
            'name' => $validated['name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'review_date' => $validated['review_date'] ?? now()->toDateString(),
            'is_published' => $validated['is_published'] ?? true,
        ]);

        return response()->json([
            'message' => 'Review berhasil ditambahkan.',
            'data' => $this->formatReview($review),
        ], 201);
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'rating' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'required', 'string', 'max:1000'],
            'review_date' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review berhasil diperbarui.',
            'data' => $this->formatReview($review),
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review berhasil dihapus.',
        ]);
    }

    private function formatReview(Review $review): array
    {
        return [
            'id' => $review->id,
            'name' => $review->name,
            'initials' => $this->makeInitials($review->name),
            'rating' => $review->rating,
            'comment' => $review->comment,
            'review_date' => optional($review->review_date)->format('Y-m-d'),
            'review_date_formatted' => $this->formatIndonesianDate($review->review_date),
            'is_published' => $review->is_published,
            'created_at' => $review->created_at,
            'updated_at' => $review->updated_at,
        ];
    }

    private function makeInitials(string $name): string
    {
        $words = collect(explode(' ', trim($name)))
            ->filter()
            ->values();

        if ($words->count() === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    private function formatIndonesianDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        $carbonDate = Carbon::parse($date);

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $carbonDate->day . ' ' . $months[$carbonDate->month] . ' ' . $carbonDate->year;
    }
}