<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports and analytics dashboard.
     */
    public function index()
    {
        // Get statistics for dashboard
        $total = \App\Models\Pwd::count();
        $totalMale = \App\Models\Pwd::where('sex', 'Male')->count();
        $totalFemale = \App\Models\Pwd::where('sex', 'Female')->count();
        $totalMinor = \App\Models\Pwd::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 0 AND 17')->count();
        $totalSenior = \App\Models\Pwd::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) >= 60')->count();
        
        // Get disability statistics (using correct table name)
        $disabilityStats = \App\Models\DisabilityType::withCount('pwdDisabilities')
        ->orderByDesc('pwd_disabilities_count')
        ->get()
        ->map(fn($d) => [
            'name'  => $d->name,
            'count' => $d->pwd_disabilities_count,
            'pct'   => $total > 0 ? round($d->pwd_disabilities_count / $total * 100) : 0,
        ]);
            
        return view('page.reports.index', compact(
            'total', 'totalMale', 'totalFemale', 'totalMinor', 'totalSenior',
            'disabilityStats'
        ));
    }
    
    /**
     * Export reports data to Excel.
     */
    public function export()
    {
        // TODO: Implement export logic
        return response()->download('reports.xlsx');
    }
}
