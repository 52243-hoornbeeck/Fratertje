<?php
namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberType;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'total' => Member::count(),
            'active' => Member::where('is_active', true)->count(),
            'archived' => Member::onlyTrashed()->count(),
            'types' => MemberType::withCount('members')->orderBy('name')->get(),
            'recent' => Member::with('memberType')->latest()->limit(5)->get(),
        ]);
    }
}
