<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UrlController extends Controller
{
    protected string $apiKey;

    public function __construct(){
        $this->apiKey = 'AIzaSyC1OBIG3E5wRlpYLuesWIvVaBA-Q64Ik_w';
    }

    public function shorten(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);
        $originalUrl = $request->input('url');

         // Check if URL is already shortened
         $existing = Url::where('original_url', $originalUrl)->first();
         if ($existing) {
             return response()->json(['short_url' => url($existing->short_url)]);
         }

        // URL check
        $response = Http::post("https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$this->apiKey}", [
            'client' => [
                'clientId' => '346460267282-31mcusgm75j7pd8l6dgb5usvfgf8l4g7.apps.googleusercontent.com',
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

        if ($response->successful() && $response->json('matches')) {
            return response()->json(['error' => 'The URL is not safe'], 422);
        }
        // Unique short URL
        do {
            $shortUrl = Str::random(6);
        } while (Url::where('short_url', $shortUrl)->exists());

        $url = Url::create([
            'original_url' => $originalUrl,
            'short_url' => $shortUrl,
        ]);

        return response()->json(['short_url' => url($shortUrl)]);
    }

    public function redirect($hash)
    {
        $url = Url::where('short_url', $hash)->firstOrFail();
        return redirect($url->original_url);
    }
}
