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
    // SQLite-compatible age calculation
    private function ageExpression(): string
    {
        return "(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) - 
                (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth))";
    }

    public function index(Request $request): View
    {
        $query = Pwd::with([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        // ── Filters ───────────────────────────────────────────────

        if ($barangay = $request->barangay) {
            $query->whereHas('residence', fn ($q) =>
                //LIKE instead of ilike (SQLite is case-insensitive by default)
                $q->where('barangay', 'LIKE', "%{$barangay}%")
            );
        }

        if ($municipality = $request->municipality) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('municipality', 'LIKE', "%{$municipality}%")
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
            //SQLite age calculation instead of EXTRACT(YEAR FROM AGE())
            $query->whereRaw(
                "(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) - 
                (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) {$condition}"
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

        if ($request->filled('is_4ps_beneficiary')) {
            $query->where('is_4ps_beneficiary', (bool) $request->is_4ps_beneficiary);
        }
        // Expiry status filter — done via subquery on date_applied
        if ($expiryStatus = $request->expiry_status) {
            $expiredBefore  = now()->subYears(5)->toDateString();
            $expiringSoon   = now()->subYears(5)->addMonths(6)->toDateString();

            match($expiryStatus) {
                'expired'  => $query->whereNotNull('date_applied')
                                    ->whereDate('date_applied', '<', $expiredBefore),
                'expiring' => $query->whereNotNull('date_applied')
                                    ->whereBetween('date_applied', [$expiredBefore, $expiringSoon]),
                'valid'    => $query->whereNotNull('date_applied')
                                    ->whereDate('date_applied', '>=', $expiringSoon),
                'unknown'  => $query->whereNull('date_applied'),
                default    => null,
            };
        }

        $pwds = $query->latest()->paginate(15)->withQueryString();

        // ── Totals ────────────────────────────────────────────────
        //count() directly instead of toBase()->getCountForPagination()
        $total       = $query->toBase()->getCountForPagination();
        $totalMale   = (clone $query)->where('sex', 'Male')->count();
        $totalFemale = (clone $query)->where('sex', 'Female')->count();
        // ── Totals ────────────────────────────────────────────────

        // SQLite-compatible age calculation using strftime
        $totalMinor  = (clone $query)->whereRaw(
            "(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) - 
            (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) BETWEEN 0 AND 17"
        )->count();

        $totalSenior = (clone $query)->whereRaw(
            "(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) - 
            (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) >= 60"
        )->count();

        // ── Age stats ─────────────────────────────────────────────
        $ageGroups = [
            '0–17 (Minor)' => ["BETWEEN 0 AND 17",  []],
            '18–29'        => ["BETWEEN 18 AND 29", []],
            '30–59'        => ["BETWEEN 30 AND 59", []],
            '60+ (Senior)' => [">= 60",             []],
        ];

        $ageStats = collect($ageGroups)->map(fn ($args, $range) => [
            'range' => $range,
            'count' => (clone $query)->whereRaw(
                "(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) - 
                (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth)) {$args[0]}"
            )->count(),
            'pct' => 0,
        ])->values()->map(function ($a) use ($total) {
            $a['pct'] = $total > 0 ? round($a['count'] / $total * 100) : 0;
            return $a;
        });
        $total4ps    = (clone $query)->where('is_4ps_beneficiary', true)->count();
        $totalNon4ps = (clone $query)->where('is_4ps_beneficiary', false)->count();

        // ── Expiry stats ──────────────────────────────────────
        $allWithDates  = Pwd::whereNotNull('date_applied')->get();
        $validCount    = $allWithDates->filter(fn ($p) => $p->id_status === 'valid')->count();
        $expiringCount = $allWithDates->filter(fn ($p) => $p->id_status === 'expiring')->count();
        $expiredCount  = $allWithDates->filter(fn ($p) => $p->id_status === 'expired')->count();
        $unknownCount  = Pwd::whereNull('date_applied')->count();

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
                // SQLite age calculation
                $this->ageExpression() . " BETWEEN ? AND ?", $bounds
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
        $total4ps    = (clone $query)->where('is_4ps_beneficiary', 1)->count();
        $totalNon4ps = (clone $query)->where('is_4ps_beneficiary', 0)->count();

        return view('page.reports.index', compact(
            'pwds',
            'total', 'totalMale', 'totalFemale', 'totalMinor', 'totalSenior',
            'total4ps', 'totalNon4ps',
            'validCount', 'expiringCount', 'expiredCount', 'unknownCount',
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