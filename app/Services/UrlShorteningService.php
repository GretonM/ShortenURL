namespace App\Services;

use App\Models\Url;
use Illuminate\Support\Facades\Http;

class UrlShorteningService
{
    public function shorten($originalUrl)
    {
        // Check if URL already exists
        $url = Url::where('original_url', $originalUrl)->first();
        if ($url) {
            return $url->short_url;
        }

        // Generate unique short URL
        do {
            $shortUrl = substr(md5(uniqid(rand(), true)), 0, 6);
        } while (Url::where('short_url', $shortUrl)->exists());

        Url::create([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl,
        ]);

        return $shortUrl;
    }

    public function isSafeUrl($originalUrl)
    {
        $apiKey = config('services.google_safe_browsing.key');
        $response = Http::post("https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$apiKey}", [
            'client' => [
                'clientId' => 'your-client-id',
                'clientVersion' => '1.5.2',
            ],
            'threatInfo' => [
                'threatTypes' => ["MALWARE", "SOCIAL_ENGINEERING"],
                'platformTypes' => ["ANY_PLATFORM"],
                'threatEntryTypes' => ["URL"],
                'threatEntries' => [
                    ['url' => $originalUrl],
                ],
            ],
        ]);

        return $response->successful() && !$response->json('matches');
    }
}
