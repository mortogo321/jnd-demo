<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortenedUrl;
use App\Services\UrlShortenerService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct(
        private readonly UrlShortenerService $urlShortenerService
    ) {}

    /**
     * Display all shortened URLs (admin only).
     */
    public function index(): JsonResponse
    {
        $urls = ShortenedUrl::query()
            ->with('user:id,name,email')
            ->withCount('urlClicks')
            ->latest()
            ->paginate(50);

        return response()->json($urls);
    }

    /**
     * Remove any shortened URL (admin only).
     */
    public function destroy(ShortenedUrl $url): JsonResponse
    {
        // Remove from cache
        $this->urlShortenerService->removeCachedUrl($url->short_code);

        $url->delete();

        return response()->json([
            'message' => 'URL deleted successfully',
        ]);
    }
}
