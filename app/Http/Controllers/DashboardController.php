<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $shortUrls = ShortUrl::query()->with(['company', 'user'])->latest();
        $companies = [];
        $teamMembers = [];

        if ($user->isSuperAdmin()) {
            $companies = Company::query()
                ->withCount('users')
                ->withSum('shortUrls', 'visits')
                ->withCount('shortUrls')
                ->latest()
                ->get();
        } elseif ($user->isAdmin()) {
            $shortUrls->where('company_id', $user->company_id);
            $teamMembers = User::query()
                ->where('company_id', $user->company_id)
                ->withCount('shortUrls')
                ->withSum('shortUrls', 'visits')
                ->orderBy('name')
                ->get();
        } else {
            $shortUrls->where('user_id', $user->id);
        }

        return view('dashboard', [
            'companies' => $companies,
            'shortUrls' => $shortUrls->paginate(10),
            'teamMembers' => $teamMembers,
        ]);
    }
}
