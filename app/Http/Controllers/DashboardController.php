<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Pwd;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Summary stats
        $totalApplicants = Pwd::count();
        $pendingCount    = 0; // No more applications
        $approvedCount   = Pwd::count(); // All registered PWDs
        $rejectedCount   = 0; // No more applications
        $cancelledCount  = 0; // No more applications

        // Recent PWDs — latest 10 with eager-loaded relations
        $recentPwd = Pwd::with([
                'residence',
                'civilStatus',
                'educationalAttainment',
                'disabilities'
            ])
            ->latest()
            ->take(10)
            ->get();

        return view('page.dashboard.index', compact(
            'totalApplicants',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'cancelledCount',
            'recentPwd',
        ));
    }
}