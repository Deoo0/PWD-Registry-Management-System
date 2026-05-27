<?php

namespace App\Http\Controllers;

use App\Models\Pwd;
use App\Models\DisabilityType;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Core counts ───────────────────────────────────────
        $total          = Pwd::count();
        $totalMale      = Pwd::where('sex', 'Male')->count();
        $totalFemale    = Pwd::where('sex', 'Female')->count();
        $totalThisMonth = Pwd::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->count();

        // ── Recent registrations (latest 10) ──────────────────
        // Eager-load every relationship the dashboard blade touches
        // to avoid N+1 queries inside the @forelse loop.
        $recentPwd = Pwd::with([
                'residence',       // $pwd->residence?->barangay
                'disabilities',    // $pwd->latestDisabilities  (see blade note below)
            ])
            ->latest()
            ->take(10)
            ->get();

        // ── Top disability types ───────────────────────────────
        // Uses the pivot table  pwd_disabilities  that your Pwd model defines.
        $disabilitySummary = DisabilityType::withCount('pwds')
            ->orderByDesc('pwds_count')
            ->take(6)
            ->get()
            ->map(fn ($d) => [
                'name'  => $d->name,
                'count' => $d->pwds_count,
                'pct'   => $total > 0
                    ? round($d->pwds_count / $total * 100)
                    : 0,
            ]);

        return view('page.dashboard.index', compact(
            'total',
            'totalMale',
            'totalFemale',
            'totalThisMonth',
            'recentPwd',
            'disabilitySummary',
        ));
    }
}