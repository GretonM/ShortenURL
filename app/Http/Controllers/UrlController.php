namespace App\Http\Controllers;

use App\Http\Requests\ShortenUrlRequest;
use App\Services\UrlShorteningService;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    protected $urlShorteningService;

    public function __construct(UrlShorteningService $urlShorteningService)
    {
        $this->urlShorteningService = $urlShorteningService;
    }

    public function shorten(ShortenUrlRequest $request)
    {
        $originalUrl = $request->input('url');

        // Check if URL is safe
        if (!$this->urlShorteningService->isSafeUrl($originalUrl)) {
            return response()->json(['error' => 'The URL is not safe'], 422);
        }

        // Shorten the URL
        $shortUrl = $this->urlShorteningService->shorten($originalUrl);

        return response()->json([
            'original_url' => $originalUrl,
            'short_url' => url($shortUrl)
        ]);
    }

    public function redirect($hash)
    {
        $url = Url::where('short_url', $hash)->firstOrFail();
        return redirect()->away($url->original_url);
    }
}
