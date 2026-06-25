@extends('layouts.app')
@section('title','PWD Registry')
@section('page-title','PWD Registry')
@section('breadcrumb')<span>›</span><span>PWD Registry</span>@endsection

@section('content')
<div class="pg">

    <div class="ph ani a1">
        <div>
            <div class="ph-t">PWD Registry</div>
            <div class="ph-s">{{ $pwds->total() }} registered persons with disabilities</div>
        </div>

        <div style="display:flex;gap:8px;">
            <button class="btn btn-o" onclick="document.getElementById('mImport').classList.add('open')">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import
            </button>
            <a href="{{ route('pwd.create') }}" class="btn btn-p">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Register PWD
            </a>
        </div>
    </div>
    
    @if($expiredCount > 0 || $expiringCount > 0)
        <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;" class="ani a2">
            @if($expiredCount > 0)
            <div style="flex:1;min-width:200px;padding:12px 16px;border-radius:10px;background:#fee2e2;border:1px solid #fca5a5;display:flex;align-items:center;gap:10px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#991b1b;">{{ $expiredCount }} Expired ID{{ $expiredCount !== 1 ? 's' : '' }}</div>
                    <div style="font-size:11px;color:#b91c1c;">PWD IDs that have passed the 5-year validity</div>
                </div>
            </div>
            @endif
            @if($expiringCount > 0)
            <div style="flex:1;min-width:200px;padding:12px 16px;border-radius:10px;background:#fef3c7;border:1px solid #fcd34d;display:flex;align-items:center;gap:10px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#92400e;">{{ $expiringCount }} Expiring Soon</div>
                    <div style="font-size:11px;color:#b45309;">PWD IDs expiring within the next 6 months</div>
                </div>
            </div>
            @endif
        </div>
        @endif

    {{-- Filters --}}
    <div class="card ani a2" style="margin-bottom:14px;">
        <div style="padding:13px 18px;">
            <form method="GET" action="{{ route('pwd.index') }}" style="display:flex;gap:9px;flex-wrap:wrap;align-items:flex-end;">
                <div class="sw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" class="sinp" placeholder="Name or PWD number…" value="{{ request('search') }}">
                </div>

                {{-- Populated from DisabilityType::all() passed by controller --}}
                <select name="disability" class="fsel" style="width:190px;">
                    <option value="">All Disability Types</option>
                    @foreach($disabilityTypes as $type)
                        <option value="{{ $type->name }}" {{ request('disability') === $type->name ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sex" class="fsel" style="width:110px;">
                    <option value="">All Sex</option>
                    <option {{ request('sex')==='Male' ? 'selected' : '' }}>Male</option>
                    <option {{ request('sex')==='Female' ? 'selected' : '' }}>Female</option>
                </select>

                {{-- Populated from distinct barangays passed by controller --}}
                <select name="barangay" class="fsel" style="width:150px;">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option {{ request('barangay') === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                    @endforeach
                </select>

                <select name="age_range" class="fsel" style="width:130px;">
                    <option value="">All Ages</option>
                    <option {{ request('age_range')==='0-17'  ? 'selected' : '' }} value="0-17">0–17 (Minor)</option>
                    <option {{ request('age_range')==='18-29' ? 'selected' : '' }} value="18-29">18–29</option>
                    <option {{ request('age_range')==='30-59' ? 'selected' : '' }} value="30-59">30–59</option>
                    <option {{ request('age_range')==='60+'   ? 'selected' : '' }} value="60+">60+ (Senior)</option>
                </select>

                <select name="is_4ps_beneficiary" class="fsel" style="width:140px;">
                    <option value="">4Ps</option>
                    <option value="1" {{ request('is_4ps_beneficiary') === '1' ? 'selected' : '' }}>4Ps Beneficiary</option>
                    <option value="0" {{ request('is_4ps_beneficiary') === '0' ? 'selected' : '' }}>Not a Beneficiary</option>
                </select>

                <button type="submit" class="btn btn-p btn-sm">Filter</button>
                <a href="{{ route('pwd.index') }}" class="btn btn-o btn-sm">Reset</a>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card ani a3">
        <div style="overflow-x:auto;">

            {{-- Disability statistics — counts passed from controller as $disabilityStats --}}
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                @foreach($disabilityStats as $stat)
                <div class="sc">
                    <div class="sc-val">{{ $stat->total }}</div>
                    <div class="sc-lbl">{{ $stat->name }}</div>
                </div>
                @endforeach
            </div>

            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>PWD Number</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Disability Type(s)</th>
                        <th>Civil Status</th>
                        <th>Barangay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pwds as $pwd)
                    <tr>
                        <td>
                            <div class="cp">{{ $pwd->last_name }}, {{ $pwd->first_name }} {{ $pwd->middle_name ?? '' }} {{ $pwd->suffix ?? '' }}</div>
                            <div class="cs">{{ $pwd->mobile_no ?? $pwd->email ?? '—' }}</div>
                        </td>
                        <td style="font-family:monospace;font-size:11.5px;color:var(--s500);">
                            {{ $pwd->pwd_number ?? '—' }}
                            @if($pwd->date_applied)
                                <div style="margin-top:3px;">
                                    @if($pwd->id_status === 'expired')
                                        <span style="font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:4px;background:#fee2e2;color:#991b1b;font-family:sans-serif;">
                                            EXPIRED
                                        </span>
                                    @elseif($pwd->id_status === 'expiring')
                                        <span style="font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:4px;background:#fef3c7;color:#92400e;font-family:sans-serif;">
                                            EXPIRING
                                        </span>
                                    @else
                                        <span style="font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:4px;background:#dcfce7;color:#166534;font-family:sans-serif;">
                                            VALID
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td><span class="badge {{ $pwd->sex === 'Male' ? 'b-m' : 'b-f' }}">{{ $pwd->sex }}</span></td>
                        <td style="font-size:13px;">
                            {{ $pwd->age }}
                            <div class="cs">{{ $pwd->date_of_birth->format('M d, Y') }}</div>
                        </td>
                        <td style="font-size:12px;max-width:160px;">
                            {{-- disabilities eager loaded via with(['disabilities']) in controller --}}
                            {{ $pwd->disabilities->pluck('name')->join(', ') ?: '—' }}
                        </td>
                        <td style="font-size:12px;">{{ $pwd->civilStatus?->name ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $pwd->residence?->barangay ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="{{ route('pwd.show', $pwd) }}" class="btn btn-o btn-sm">View</a>
                                <a href="{{ route('pwd.edit', $pwd) }}" class="btn btn-o btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty">
                                <div class="empty-t">No PWDs registered yet</div>
                                <div class="empty-s"><a href="{{ route('pwd.create') }}" style="color:var(--blue);">Register the first one</a></div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pag">
            {{ $pwds->appends(request()->query())->links('vendor.pagination.custom') }}
            <span class="pi">{{ $pwds->firstItem() }}–{{ $pwds->lastItem() }} of {{ $pwds->total() }}</span>
        </div>
    </div>

</div>

{{-- ── IMPORT MODAL ── --}}
<div class="mbg" id="mImport">
        <div class="modal">
            <div class="mhd">
                <div class="mt">Import PWDs from Excel</div>
                <button class="mx" onclick="document.getElementById('mImport').classList.remove('open')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('pwd.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mbd">
                    <div style="padding:12px;background:var(--blue-lt);border-radius:8px;border:1px solid var(--blue);margin-bottom:14px;">
                        <p style="font-size:12px;color:var(--blue);font-weight:600;margin-bottom:4px;">Before importing:</p>
                        <ul style="font-size:11.5px;color:var(--s600);padding-left:16px;line-height:1.8;">
                            <li>Download the template and fill it in</li>
                            <li>Civil status, education, and occupation must match exactly</li>
                            <li>Disability types are comma-separated (e.g. "Visual Disability, Mental Disability")</li>
                            <li>Date format: YYYY-MM-DD</li>
                            <li>Rows with errors are skipped — valid rows still import</li>
                        </ul>
                    </div>
                    <a href="{{ route('pwd.template') }}" class="btn btn-o btn-sm" style="margin-bottom:14px;display:inline-flex;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Template
                    </a>
                    <div class="fg">
                        <label class="fl">Excel / CSV File <span style="color:var(--red)">*</span></label>
                        <input type="file" name="file" class="fi @error('file') err @enderror" accept=".xlsx,.xls,.csv" required>
                        @error('file')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mft">
                    <button type="button" class="btn btn-o" onclick="document.getElementById('mImport').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn btn-p">Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Import errors --}}
    @if(session('import_errors') && count(session('import_errors')) > 0)
    <div style="position:fixed;bottom:20px;right:20px;max-width:380px;background:white;border:1px solid var(--s200);border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);padding:14px;z-index:999;">
        <p style="font-size:12px;font-weight:700;color:var(--red);margin-bottom:8px;">Some rows were skipped:</p>
        <ul style="font-size:11.5px;color:var(--s600);padding-left:16px;max-height:200px;overflow-y:auto;line-height:1.8;">
            @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button onclick="this.parentElement.remove()" style="margin-top:8px;font-size:11px;color:var(--s400);background:none;border:none;cursor:pointer;">Dismiss</button>
    </div>
    @endif
@endsection