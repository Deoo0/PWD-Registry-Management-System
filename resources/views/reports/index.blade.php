@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports')
@section('breadcrumb')<span>›</span><span>Reports</span>@endsection

@section('content')
<div class="pg">

    <div class="ph ani a1">
        <div>
            <div class="ph-t">Reports &amp; Analytics</div>
            <div class="ph-s">Summary of registered PWDs across all categories</div>
        </div>
        {{-- Backend: wire to ReportController@export --}}
        <a href="{{ route('reports.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-p">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export to Excel
        </a>
    </div>

    {{-- ── Filter bar ── --}}
    <div class="card ani a1" style="margin-bottom:18px;">
        <div style="padding:14px 18px;">
            <p style="font-size:9.5px;font-weight:700;color:var(--s400);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Filter Report</p>
            {{--
                Backend: form submits GET to route('reports.index')
                Controller filters $pwds based on request params
            --}}
            <form method="GET" action="{{ route('reports.index') }}" style="display:flex;gap:9px;flex-wrap:wrap;align-items:flex-end;">
                <div>
                    <label class="fl">Barangay</label>
                    <input type="text" name="barangay" class="fi" style="width:150px;" placeholder="e.g. Abucay" value="{{ request('barangay') }}">
                </div>
                <div>
                    <label class="fl">Municipality</label>
                    <input type="text" name="municipality" class="fi" style="width:150px;" value="{{ request('municipality') }}">
                </div>
                <div>
                    <label class="fl">Sex</label>
                    <select name="sex" class="fsel" style="width:110px;">
                        <option value="">All</option>
                        <option {{ request('sex')==='Male'?'selected':'' }}>Male</option>
                        <option {{ request('sex')==='Female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="fl">Age Range</label>
                    <select name="age_range" class="fsel" style="width:130px;">
                        <option value="">All Ages</option>
                        <option {{ request('age_range')==='0-17'?'selected':'' }} value="0-17">0–17 (Minor)</option>
                        <option {{ request('age_range')==='18-29'?'selected':'' }} value="18-29">18–29</option>
                        <option {{ request('age_range')==='30-59'?'selected':'' }} value="30-59">30–59</option>
                        <option {{ request('age_range')==='60+'?'selected':'' }} value="60+">60+ (Senior)</option>
                    </select>
                </div>
                <div>
                    <label class="fl">Disability Type</label>
                    <select name="disability" class="fsel" style="width:200px;">
                        <option value="">All Types</option>
                        @foreach(\App\Models\DisabilityType::all() as $dt)
                            <option {{ request('disability')===$dt->name?'selected':'' }}>{{ $dt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fl">Civil Status</label>
                    <select name="civil_status" class="fsel" style="width:160px;">
                        <option value="">All</option>
                        @foreach(\App\Models\CivilStatus::all() as $cs)
                            <option {{ request('civil_status')===$cs->name?'selected':'' }}>{{ $cs->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:7px;align-items:flex-end;">
                    <button type="submit" class="btn btn-p btn-sm">Generate</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-o btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Top stat cards ── --}}
    {{--
        Backend: pass from ReportController:
        $total, $totalMale, $totalFemale, $totalMinor, $totalSenior
    --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:18px;" class="ani a2">
        @foreach([
            ['Total PWDs',   $total??0,        'linear-gradient(90deg,var(--navy),var(--blue-h))', 'var(--blue-lt)',  'var(--blue)'],
            ['Male',         $totalMale??0,     'linear-gradient(90deg,#1e40af,#3b82f6)',           'var(--blue-lt)',  'var(--blue)'],
            ['Female',       $totalFemale??0,   'linear-gradient(90deg,#be185d,#ec4899)',           '#fce7f3',        '#be185d'],
            ['Minors (0–17)',$totalMinor??0,    'linear-gradient(90deg,var(--gold),#f97316)',       'var(--gold-lt)', 'var(--gold)'],
            ['Seniors (60+)',$totalSenior??0,   'linear-gradient(90deg,var(--green),#10b981)',      'var(--green-lt)','var(--green)'],
        ] as $i => [$lbl,$val,$bar,$ibg,$icol])
        <div class="sc">
            <div class="sc-bar" style="background:{{ $bar }};"></div>
            <div class="sc-ic" style="background:{{ $ibg }};color:{{ $icol }};">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:17px;height:17px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="sc-v">{{ $val }}</div>
            <div class="sc-l">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Row 1: Disability type + Sex + Civil Status ── --}}
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:14px;margin-bottom:14px;" class="ani a3">

        {{-- Disability Type Breakdown --}}
        <div class="card">
            <div class="card-hd">
                <div class="card-t">By Disability Type</div>
                {{-- Backend: count of applicants per disability type --}}
            </div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: pass $disabilityStats as:
                    [['name'=>'Physical Disability','count'=>42,'pct'=>38], ...]

                    Query:
                    DisabilityType::withCount(['applicationDisabilities as count'])
                        ->orderByDesc('count')
                        ->get()
                        ->map(fn($d) => [
                            'name'  => $d->name,
                            'count' => $d->count,
                            'pct'   => $total > 0 ? round($d->count / $total * 100) : 0,
                        ])
                --}}
                @php
                    $dcols = ['#1549a8','#d97706','#047857','#b91c1c','#7c3aed','#0891b2','#ea580c','#4f46e5','#0f172a','#475569'];
                    $dph = [
                        'Physical Disability (Orthopedic)',
                        'Visual Disability',
                        'Deaf or Hard of Hearing',
                        'Mental Disability',
                        'Intellectual Disability',
                        'Psychosocial Disability',
                        'Learning Disability',
                        'Speech and Language Impairment',
                        'Cancer (RA11215)',
                        'Rare Disease (RA10747)',
                    ];
                @endphp
                @foreach($dph as $i => $name)
                {{--
                    Backend: replace $name/0/0 with:
                    $d['name'] / $d['count'] / $d['pct']
                --}}
                <div class="bar-row">
                    <div class="bar-lbl">{{ $name }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:0%;background:{{ $dcols[$i] }};"></div></div>
                    <div class="bar-cnt">0</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Sex Distribution --}}
        <div class="card">
            <div class="card-hd"><div class="card-t">By Sex</div></div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: $totalMale, $totalFemale, $total
                    $mPct = $total > 0 ? round($totalMale / $total * 100) : 0;
                --}}
                @php
                    $mPct = ($total??0) > 0 ? round(($totalMale??0) / ($total??1) * 100) : 0;
                    $fPct = 100 - $mPct;
                @endphp

                {{-- Visual bar --}}
                <div style="height:10px;border-radius:5px;overflow:hidden;display:flex;margin-bottom:16px;background:var(--s100);">
                    <div style="width:{{ $mPct }}%;background:var(--blue);transition:width .7s ease;"></div>
                    <div style="width:{{ $fPct }}%;background:#ec4899;"></div>
                </div>

                @foreach([['Male','var(--blue)',$totalMale??0,$mPct],['Female','#ec4899',$totalFemale??0,$fPct]] as [$lbl,$col,$cnt,$pct])
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;background:var(--s50);margin-bottom:8px;border:1px solid var(--s200);">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:10px;height:10px;border-radius:3px;background:{{ $col }};"></div>
                        <span style="font-size:12.5px;color:var(--s600);">{{ $lbl }}</span>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-family:'Playfair Display',serif;font-size:20px;color:var(--ink);line-height:1;">{{ $cnt }}</div>
                        <div style="font-size:10px;color:var(--s400);">{{ $pct }}%</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Civil Status --}}
        <div class="card">
            <div class="card-hd"><div class="card-t">By Civil Status</div></div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: pass $civilStatusStats as:
                    [['name'=>'Single','count'=>30,'pct'=>27], ...]

                    Query:
                    CivilStatus::withCount('applicants')
                        ->orderByDesc('applicants_count')
                        ->get()
                        ->map(fn($cs) => [
                            'name'  => $cs->name,
                            'count' => $cs->applicants_count,
                            'pct'   => $total > 0 ? round($cs->applicants_count / $total * 100) : 0,
                        ])
                --}}
                @php
                    $cscols = ['#1549a8','#7c3aed','#047857','#d97706','#b91c1c'];
                    $csph = ['Single','Married','Widow/er','Separated','Cohabitation (live-in)'];
                @endphp
                @foreach($csph as $i => $name)
                <div class="bar-row" style="margin-bottom:10px;">
                    <div class="bar-lbl" style="width:140px;">{{ $name }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:0%;background:{{ $cscols[$i] }};"></div></div>
                    <div class="bar-cnt">0</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Row 2: Age range + Education + Occupation ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;" class="ani a4">

        {{-- Age Range --}}
        <div class="card">
            <div class="card-hd"><div class="card-t">By Age Range</div></div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: pass $ageStats as:
                    [['range'=>'0–17','count'=>12,'pct'=>10], ...]

                    Query example:
                    $ageGroups = [
                        '0–17'  => Applicant::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 0 AND 17')->count(),
                        '18–29' => Applicant::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 18 AND 29')->count(),
                        '30–59' => Applicant::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 30 AND 59')->count(),
                        '60+'   => Applicant::whereRaw('EXTRACT(YEAR FROM AGE(date_of_birth)) >= 60')->count(),
                    ];
                --}}
                @php
                    $ageCols  = ['#f59e0b','#1549a8','#047857','#7c3aed'];
                    $agePh    = ['0–17 (Minor)','18–29','30–59','60+ (Senior)'];
                @endphp
                @foreach($agePh as $i => $range)
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:12px;color:var(--s600);">{{ $range }}</span>
                        <span style="font-size:12px;font-weight:700;color:var(--ink);">0 <span style="color:var(--s400);font-weight:400;">(0%)</span></span>
                    </div>
                    <div class="bar-track" style="height:9px;">
                        <div class="bar-fill" style="width:0%;height:9px;background:{{ $ageCols[$i] }};border-radius:5px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Educational Attainment --}}
        <div class="card">
            <div class="card-hd"><div class="card-t">By Educational Attainment</div></div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: pass $educationStats as:
                    [['name'=>'Elementary','count'=>20,'pct'=>18], ...]

                    Query:
                    EducationalAttainment::withCount('applicants')
                        ->orderByDesc('applicants_count')
                        ->get()
                        ->map(fn($ea) => [
                            'name'  => $ea->name,
                            'count' => $ea->applicants_count,
                            'pct'   => $total > 0 ? round($ea->applicants_count / $total * 100) : 0,
                        ])
                --}}
                @php
                    $edCols = ['#1549a8','#047857','#d97706','#7c3aed','#b91c1c','#0891b2','#ea580c','#4f46e5'];
                    $edPh   = ['Post Graduate','College','Vocational','Senior High School','Junior High School','Elementary','Kindergarten','None'];
                @endphp
                @foreach($edPh as $i => $name)
                <div class="bar-row" style="margin-bottom:8px;">
                    <div class="bar-lbl" style="width:140px;font-size:11.5px;">{{ $name }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:0%;background:{{ $edCols[$i] }};"></div></div>
                    <div class="bar-cnt">0</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Occupation --}}
        <div class="card">
            <div class="card-hd"><div class="card-t">By Occupation</div></div>
            <div style="padding:16px 18px;">
                {{--
                    Backend: pass $occupationStats as:
                    [['name'=>'Managers','count'=>5,'pct'=>4], ...]

                    Query:
                    Occupation::withCount('applicants')
                        ->orderByDesc('applicants_count')
                        ->get()
                        ->map(fn($occ) => [
                            'name'  => $occ->name,
                            'count' => $occ->applicants_count,
                            'pct'   => $total > 0 ? round($occ->applicants_count / $total * 100) : 0,
                        ])
                --}}
                @php
                    $occCols = ['#1549a8','#047857','#d97706','#7c3aed','#b91c1c','#0891b2','#ea580c','#4f46e5','#0f172a','#64748b','#be185d'];
                    $occPh   = ['Managers','Professionals','Technicians & Associates','Clerical Support','Service & Sales','Skilled Agricultural','Craft & Trade Workers','Plant & Machine Operators','Elementary Occupations','Armed Forces','Others'];
                @endphp
                @foreach($occPh as $i => $name)
                <div class="bar-row" style="margin-bottom:7px;">
                    <div class="bar-lbl" style="width:140px;font-size:11.5px;">{{ $name }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:0%;background:{{ $occCols[$i] }};"></div></div>
                    <div class="bar-cnt">0</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Row 3: Barangay breakdown + raw data table ── --}}
    <div style="display:grid;grid-template-columns:320px 1fr;gap:14px;" class="ani a5">

        {{-- Barangay breakdown --}}
        <div class="card">
            <div class="card-hd">
                <div class="card-t">By Barangay</div>
                {{-- Backend: top 10 barangays --}}
            </div>
            <div style="padding:16px 18px;max-height:400px;overflow-y:auto;">
                {{--
                    Backend: pass $barangayStats as:
                    [['barangay'=>'Abucay','count'=>15,'pct'=>13], ...]

                    Query:
                    Residence::select('barangay', DB::raw('count(*) as count'))
                        ->groupBy('barangay')
                        ->orderByDesc('count')
                        ->take(10)
                        ->get()
                        ->map(fn($r) => [
                            'barangay' => $r->barangay,
                            'count'    => $r->count,
                            'pct'      => $total > 0 ? round($r->count / $total * 100) : 0,
                        ])
                --}}
                <div class="empty" style="padding:30px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div class="empty-t">No data yet</div>
                    <div class="empty-s">Register PWDs to see barangay breakdown</div>
                </div>
                {{--
                    Replace empty state with:
                    @foreach($barangayStats as $i => $b)
                    <div class="bar-row">
                        <div style="width:16px;height:16px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:white;background:var(--blue);flex-shrink:0;">{{ $i+1 }}</div>
                        <div class="bar-lbl" style="width:110px;">{{ $b['barangay'] }}</div>
                        <div class="bar-track"><div class="bar-fill" style="width:{{ $b['pct'] }}%;background:var(--blue);"></div></div>
                        <div class="bar-cnt">{{ $b['count'] }}</div>
                    </div>
                    @endforeach
                --}}
            </div>
        </div>

        {{-- Raw data table --}}
        <div class="card">
            <div class="card-hd">
                <div>
                    <div class="card-t">Registry Data</div>
                    {{-- Backend: {{ $pwds->total() }} records --}}
                    <div class="card-st">0 matching records</div>
                </div>
                <a href="{{ route('reports.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-o btn-sm">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </a>
            </div>
            <div style="overflow-x:auto;max-height:360px;overflow-y:auto;">
                <table class="tbl">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Disability</th>
                            <th>Civil Status</th>
                            <th>Education</th>
                            <th>Occupation</th>
                            <th>Barangay</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{--
                            Backend: replace with:
                            @forelse($pwds as $pwd)
                            <tr>
                                <td><div class="cp" style="font-size:12px;">{{ $pwd->last_name }}, {{ $pwd->first_name }}</div></td>
                                <td><span class="badge {{ $pwd->sex === 'Male' ? 'b-m' : 'b-f' }}">{{ $pwd->sex }}</span></td>
                                <td style="font-size:12px;">{{ $pwd->age }}</td>
                                <td style="font-size:11.5px;max-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $pwd->latestApplication?->disabilities->pluck('name')->join(', ') ?: '—' }}
                                </td>
                                <td style="font-size:12px;">{{ $pwd->civilStatus?->name ?? '—' }}</td>
                                <td style="font-size:12px;">{{ $pwd->educationalAttainment?->name ?? '—' }}</td>
                                <td style="font-size:12px;">{{ $pwd->occupation?->name ?? '—' }}</td>
                                <td style="font-size:12px;">{{ $pwd->residence?->barangay ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8"><div class="empty"><div class="empty-t">No records match</div></div></td></tr>
                            @endforelse
                        --}}
                        <tr><td colspan="8"><div class="empty">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div class="empty-t">Connect Backend Later</div>
                        </div></td></tr>
                    </tbody>
                </table>
            </div>
            {{-- Backend: {{ $pwds->appends(request()->query())->links() }} --}}
            <div class="pag">
                <a href="#" class="pb on">1</a>
                <span class="pi">0 records</span>
            </div>
        </div>
    </div>

</div>
@endsection