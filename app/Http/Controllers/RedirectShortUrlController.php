<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class RedirectShortUrlController extends Controller
{
    public function __invoke(string $shortCode): RedirectResponse
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->firstOrFail();
        $shortUrl->increment('visits');

        return redirect()->away($shortUrl->original_url);
    }
}
