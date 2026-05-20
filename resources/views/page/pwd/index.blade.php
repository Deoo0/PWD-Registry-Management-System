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
        <a href="{{ route('pwd.create') }}" class="btn btn-p">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Register PWD
        </a>
    </div>

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
                            <div class="cp">{{ $pwd->last_name }}, {{ $pwd->first_name }} {{ $pwd->middle_name ? substr($pwd->middle_name,0,1).'.' : '' }} {{ $pwd->suffix ?? '' }}</div>
                            <div class="cs">{{ $pwd->mobile_no ?? $pwd->email ?? '—' }}</div>
                        </td>
                        <td style="font-family:monospace;font-size:11.5px;color:var(--s500);">{{ $pwd->pwd_number ?? '—' }}</td>
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
@endsection