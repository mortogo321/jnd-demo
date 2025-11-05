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
     *
     * @OA\Get(
     *     path="/api/urls",
     *     summary="Get user's shortened URLs",
     *     tags={"URLs"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of shortened URLs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="original_url", type="string"),
     *                 @OA\Property(property="short_code", type="string"),
     *                 @OA\Property(property="url_clicks_count", type="integer")
     *             )
     *         )
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/api/urls",
     *     summary="Create shortened URL",
     *     tags={"URLs"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", format="url", example="https://example.com/very/long/url")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="URL shortened successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="url", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     *
     * @OA\Get(
     *     path="/api/urls/{id}",
     *     summary="Get URL details with analytics",
     *     tags={"URLs"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL details with analytics",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="URL not found")
     * )
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
     *
     * @OA\Delete(
     *     path="/api/urls/{id}",
     *     summary="Delete shortened URL",
     *     tags={"URLs"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="URL not found")
     * )
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
     *
     * @OA\Get(
     *     path="/{code}",
     *     summary="Redirect to original URL",
     *     tags={"URLs"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to original URL"
     *     ),
     *     @OA\Response(response=404, description="URL not found")
     * )
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
