<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Models\ShortenedUrl;
use App\Models\UrlClick;
use App\Services\UrlShortenerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function __construct(
        private readonly UrlShortenerService $urlShortenerService
    ) {}

    /**
     * Display a listing of the user's shortened URLs.
     */
    public function index(Request $request): JsonResponse
    {
        $urls = ShortenedUrl::query()
            ->where('user_id', $request->user()->id)
            ->with(['urlClicks' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->withCount('urlClicks')
            ->latest()
            ->get();

        return response()->json([
            'urls' => $urls,
        ]);
    }

    /**
     * Store a newly created shortened URL.
     */
    public function store(StoreUrlRequest $request): JsonResponse
    {
        $shortenedUrl = $this->urlShortenerService->shortenUrl(
            $request->input('original_url'),
            $request->user()
        );

        return response()->json([
            'message' => 'URL shortened successfully',
            'url' => $shortenedUrl,
        ], 201);
    }

    /**
     * Display the specified shortened URL with analytics.
     */
    public function show(Request $request, ShortenedUrl $url): JsonResponse
    {
        // Ensure the user owns this URL
        if ($url->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $url->load(['urlClicks' => function ($query) {
            $query->latest()->limit(100);
        }]);

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Remove the specified shortened URL.
     */
    public function destroy(Request $request, ShortenedUrl $url): JsonResponse
    {
        // Ensure the user owns this URL
        if ($url->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Remove from cache
        $this->urlShortenerService->removeCachedUrl($url->short_code);

        $url->delete();

        return response()->json([
            'message' => 'URL deleted successfully',
        ]);
    }

    /**
     * Redirect to the original URL and track the click.
     */
    public function redirect(Request $request, string $code): RedirectResponse|JsonResponse
    {
        // Get URL from cache or database
        $shortenedUrl = $this->urlShortenerService->getUrlByCode($code);

        if (! $shortenedUrl) {
            return response()->json([
                'message' => 'URL not found',
            ], 404);
        }

        // Track the click asynchronously for better performance
        UrlClick::query()->create([
            'shortened_url_id' => $shortenedUrl->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
        ]);

        // Increment click counter
        $shortenedUrl->incrementClicks();

        // Redirect to original URL
        return redirect($shortenedUrl->original_url);
    }
}
