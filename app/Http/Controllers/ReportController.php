<?php

namespace App\Http\Controllers;

use App\Models\CivilStatus;
use App\Models\DisabilityType;
use App\Models\EducationalAttainment;
use App\Models\Occupation;
use App\Models\Pwd;
use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Exports\PwdExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pwd::with([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        // Apply filters
        if ($barangay = $request->barangay) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('barangay', 'ilike', "%{$barangay}%")
            );
        }

        if ($municipality = $request->municipality) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('municipality', 'ilike', "%{$municipality}%")
            );
        }

        if ($sex = $request->sex) {
            $query->where('sex', $sex);
        }

        if ($ageRange = $request->age_range) {
            [$min, $max] = match($ageRange) {
                '0-17'  => [0, 17],
                '18-29' => [18, 29],
                '30-59' => [30, 59],
                '60+'   => [60, 150],
                default => [0, 150],
            };
            $query->whereRaw(
                "EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN ? AND ?",
                [$min, $max]
            );
        }

        if ($disability = $request->disability) {
            $query->whereHas('disabilities', fn ($q) =>
                $q->where('name', $disability)
            );
        }

        if ($civilStatus = $request->civil_status) {
            $query->whereHas('civilStatus', fn ($q) =>
                $q->where('name', $civilStatus)
            );
        }

        $pwds = $query->latest()->paginate(15)->withQueryString();

        // ── Totals ────────────────────────────────────────────────
        $total       = $query->toBase()->getCountForPagination();
        $totalMale   = (clone $query)->where('sex', 'Male')->count();
        $totalFemale = (clone $query)->where('sex', 'Female')->count();
        $totalMinor  = (clone $query)->whereRaw("EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 0 AND 17")->count();
        $totalSenior = (clone $query)->whereRaw("EXTRACT(YEAR FROM AGE(date_of_birth)) >= 60")->count();

        // ── Disability stats ──────────────────────────────────────
        $disabilityStats = DisabilityType::withCount(['pwds' => function ($q) use ($query) {
            $q->whereIn('pwds.id', (clone $query)->select('pwds.id'));
        }])->orderByDesc('pwds_count')->get()->map(fn ($d) => [
            'name'  => $d->name,
            'count' => $d->pwds_count,
            'pct'   => $total > 0 ? round($d->pwds_count / $total * 100) : 0,
        ]);

        // ── Civil status stats ────────────────────────────────────
        $civilStatusStats = CivilStatus::withCount(['pwds' => function ($q) use ($query) {
            $q->whereIn('pwds.id', (clone $query)->select('pwds.id'));
        }])->orderByDesc('pwds_count')->get()->map(fn ($cs) => [
            'name'  => $cs->name,
            'count' => $cs->pwds_count,
            'pct'   => $total > 0 ? round($cs->pwds_count / $total * 100) : 0,
        ]);

        // ── Age stats ─────────────────────────────────────────────
        $ageGroups = [
            '0–17 (Minor)' => [0, 17],
            '18–29'        => [18, 29],
            '30–59'        => [30, 59],
            '60+ (Senior)' => [60, 150],
        ];
        $ageStats = collect($ageGroups)->map(fn ($bounds, $range) => [
            'range' => $range,
            'count' => (clone $query)->whereRaw(
                "EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN ? AND ?", $bounds
            )->count(),
            'pct' => 0,
        ])->values()->map(function ($a) use ($total) {
            $a['pct'] = $total > 0 ? round($a['count'] / $total * 100) : 0;
            return $a;
        });

        // ── Education stats ───────────────────────────────────────
        $educationStats = EducationalAttainment::withCount(['pwds' => function ($q) use ($query) {
            $q->whereIn('pwds.id', (clone $query)->select('pwds.id'));
        }])->orderByDesc('pwds_count')->get()->map(fn ($ea) => [
            'name'  => $ea->name,
            'count' => $ea->pwds_count,
            'pct'   => $total > 0 ? round($ea->pwds_count / $total * 100) : 0,
        ]);

        // ── Occupation stats ──────────────────────────────────────
        $occupationStats = Occupation::withCount(['pwds' => function ($q) use ($query) {
            $q->whereIn('pwds.id', (clone $query)->select('pwds.id'));
        }])->orderByDesc('pwds_count')->get()->map(fn ($occ) => [
            'name'  => $occ->name,
            'count' => $occ->pwds_count,
            'pct'   => $total > 0 ? round($occ->pwds_count / $total * 100) : 0,
        ]);

        // ── Barangay stats ────────────────────────────────────────
        $barangayStats = Residence::select('barangay', DB::raw('count(*) as count'))
            ->whereIn('id', (clone $query)->select('residence_id'))
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->take(10)
            ->get()
            ->map(fn ($r) => [
                'barangay' => $r->barangay,
                'count'    => $r->count,
                'pct'      => $total > 0 ? round($r->count / $total * 100) : 0,
            ]);

        // ── Filter dropdowns ──────────────────────────────────────
        $disabilityTypes = DisabilityType::orderBy('name')->get();
        $civilStatuses   = CivilStatus::orderBy('name')->get();

        return view('page.reports.index', compact(
            'pwds',
            'total', 'totalMale', 'totalFemale', 'totalMinor', 'totalSenior',
            'disabilityStats', 'civilStatusStats', 'ageStats',
            'educationStats', 'occupationStats', 'barangayStats',
            'disabilityTypes', 'civilStatuses'
        ));
    }

    public function export(Request $request)
    {
        $filename = 'pwd-registry-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PwdExport($request), $filename);
    }
}