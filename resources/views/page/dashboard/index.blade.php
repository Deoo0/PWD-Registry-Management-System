@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="pg">

    {{-- Welcome banner --}}
    <div class="ani a1" style="background:linear-gradient(135deg,var(--navy) 0%,#1a4fa8 100%);border-radius:12px;padding:20px 26px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;overflow:hidden;position:relative;">
        <div style="position:absolute;right:-10px;top:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.04);"></div>
        <div style="position:absolute;right:70px;bottom:-30px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.03);"></div>
        <div>
            <p style="font-size:10.5px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px;">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},</p>
            <h2 style="font-family:'Playfair Display',serif;font-size:20px;color:white;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
            <p style="font-size:11.5px;color:rgba(255,255,255,.38);margin-top:2px;">{{ Auth::user()->usertype->name }} &middot; {{ now()->format('F j, Y') }}</p>
        </div>
        <div style="text-align:right;background:rgba(253,216,53,.1);border:1px solid rgba(253,216,53,.2);border-radius:9px;padding:10px 16px;">
            <p style="font-size:9.5px;color:#FDD835;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Philippine Registry for PWDs</p>
            <p style="font-size:11px;color:rgba(255,255,255,.35);margin-top:2px;">Version 4.0 · DSWD</p>
        </div>
    </div>

    {{-- Stats --}}
    {{--
        Backend: pass from DashboardController:
        $total, $totalMale, $totalFemale, $totalThisMonth
    --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
        @foreach([
            ['Total Registered PWDs', $total??0,          'linear-gradient(90deg,var(--navy),var(--blue-h))', 'var(--blue-lt)',  'var(--blue)',  'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Male',                  $totalMale??0,      'linear-gradient(90deg,#1e40af,#3b82f6)',            'var(--blue-lt)',  'var(--blue)',  'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['Female',                $totalFemale??0,    'linear-gradient(90deg,#be185d,#ec4899)',            '#fce7f3',        '#be185d',     'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['Registered This Month', $totalThisMonth??0, 'linear-gradient(90deg,var(--green),#10b981)',       'var(--green-lt)','var(--green)', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ] as $i => [$lbl, $val, $bar, $icbg, $iccol, $path])
        <div class="sc ani a{{ $i+2 }}">
            <div class="sc-bar" style="background:{{ $bar }};"></div>
            <div class="sc-ic" style="background:{{ $icbg }};color:{{ $iccol }};">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
            </div>
            <div class="sc-v">{{ $val }}</div>
            <div class="sc-l">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>

    {{-- Two columns --}}
    <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;" class="ani a3">

        {{-- Recent registrations --}}
        <div class="card">
            <div class="card-hd">
                <div>
                    <div class="card-t">Recently Registered</div>
                    <div class="card-st">Latest 10 PWD records</div>
                </div>
                <a href="{{ route('pwd.index') }}" class="btn btn-o btn-sm">View all</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>PWD #</th>
                            <th>Sex</th>
                            <th>Disability</th>
                            <th>Barangay</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                            @forelse($recentPwd as $pwd)
                            <tr onclick="window.location='{{ route('pwd.show', $pwd) }}'" style="cursor:pointer;">
                                <td>
                                    <div class="cp">{{ $pwd->last_name }}, {{ $pwd->first_name }}</div>
                                    <div class="cs">{{ $pwd->date_of_birth->format('M d, Y') }} · Age {{ $pwd->age }}</div>
                                </td>
                                <td style="font-family:monospace;font-size:11.5px;color:var(--s500);">{{ $pwd->pwd_number ?? '—' }}</td>
                                <td><span class="badge {{ $pwd->sex === 'Male' ? 'b-m' : 'b-f' }}">{{ $pwd->sex }}</span></td>
                                <td style="font-size:12px;max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $pwd->latestDisabilities ?? '—' }}
                                </td>
                                <td style="font-size:12px;">{{ $pwd->residence?->barangay ?? '—' }}</td>
                                <td style="font-size:11.5px;color:var(--s400);white-space:nowrap;">{{ $pwd->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6"><div class="empty"><div class="empty-t">No records yet</div></div></td></tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right column --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Top disability types --}}
            <div class="card">
                <div class="card-hd"><div class="card-t">Top Disabilities</div></div>
                <div style="padding:14px 18px;">
                    {{--
                        Backend: pass $disabilitySummary as:
                        [['name'=>'Physical Disability','count'=>42,'pct'=>40], ...]

                        Query:
                        DisabilityType::withCount('applicationDisabilities')
                            ->orderByDesc('application_disabilities_count')
                            ->take(6)->get()
                            ->map(fn($d) => [
                                'name'  => $d->name,
                                'count' => $d->application_disabilities_count,
                                'pct'   => $total > 0 ? round($d->application_disabilities_count / $total * 100) : 0,
                            ])
                    --}}
                    @php
                        $dcols = ['#1549a8','#d97706','#047857','#b91c1c','#7c3aed','#0891b2'];
                        $dph   = [['Physical Disability',0,0],['Visual Disability',0,0],['Deaf/Hard of Hearing',0,0],['Mental Disability',0,0],['Intellectual',0,0],['Others',0,0]];
                    @endphp
                    @foreach($dph as $i => [$n,$c,$p])
                    <div class="bar-row">
                        <div class="bar-lbl" style="width:130px;">{{ $n }}</div>
                        <div class="bar-track"><div class="bar-fill" style="width:{{ $p }}%;background:{{ $dcols[$i] }};"></div></div>
                        <div class="bar-cnt">{{ $c }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Sex split --}}
            <div class="card">
                <div class="card-hd"><div class="card-t">Sex Distribution</div></div>
                <div style="padding:14px 18px;display:flex;gap:10px;">
                    {{-- Backend: $totalMale, $totalFemale, $total --}}
                    @php
                        $mPct = ($total??0) > 0 ? round(($totalMale??0)/($total??1)*100) : 0;
                        $fPct = 100 - $mPct;
                    @endphp
                    <div style="flex:1;text-align:center;padding:12px;background:var(--blue-xlt);border-radius:9px;">
                        <div style="font-family:'Playfair Display',serif;font-size:26px;color:var(--blue);">{{ $totalMale??0 }}</div>
                        <div style="font-size:11px;color:var(--s500);margin-top:1px;">Male</div>
                        <div style="font-size:11px;color:var(--blue);font-weight:700;margin-top:4px;">{{ $mPct }}%</div>
                    </div>
                    <div style="flex:1;text-align:center;padding:12px;background:#fce7f3;border-radius:9px;">
                        <div style="font-family:'Playfair Display',serif;font-size:26px;color:#be185d;">{{ $totalFemale??0 }}</div>
                        <div style="font-size:11px;color:var(--s500);margin-top:1px;">Female</div>
                        <div style="font-size:11px;color:#be185d;font-weight:700;margin-top:4px;">{{ $fPct }}%</div>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="card">
                <div class="card-hd"><div class="card-t">Quick Actions</div></div>
                <div style="padding:12px;display:flex;flex-direction:column;gap:7px;">
                    <a href="{{ route('pwd.create') }}" class="btn btn-p" style="justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Register New PWD
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn btn-o" style="justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection