<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->canCreateShortUrls(), 403, 'SuperAdmin cannot create short URLs.');

        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $validated['original_url'],
            'short_code' => $this->generateCode(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Short URL generated.');
    }

    private function generateCode(): string
    {
        do {
            $code = Str::random(7);
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }
}
