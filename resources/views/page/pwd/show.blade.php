@extends('layouts.app')

@section('title', 'PWD Details')
@section('page-title', 'PWD Record Details')
@section('breadcrumb')
    <span>›</span>
    <a href="{{ route('pwd.index') }}">PWD Registry</a>
    <span>›</span>
    <span>View Details</span>
@endsection

@section('content')
<div class="pg">

    <div class="ph ani a1">
        <div>
            <div class="ph-t">PWD Record Details</div>
            <div class="ph-s">View complete PWD information</div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('pwd.edit', $pwd) }}" class="btn btn-o">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form method="POST" action="{{ route('pwd.destroy', $pwd) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-d" onclick="return confirm('Are you sure you want to delete this PWD record?')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

        {{-- Left: Main information --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Personal Information --}}
            <div class="card ani a2">
                <div class="card-hd">
                    <div class="card-t">Personal Information</div>
                </div>
                <div class="mbd">
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                        <div>
                            <div class="fl">Full Name</div>
                            <div class="fv">{{ $pwd->last_name }}, {{ $pwd->first_name }} {{ $pwd->middle_name ? substr($pwd->middle_name,0,1).'.' : '' }} {{ $pwd->suffix ?? '' }}</div>
                        </div>
                        <div>
                            <div class="fl">PWD Number</div>
                            <div class="fv" style="font-family:monospace;">{{ $pwd->pwd_number ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Date of Birth</div>
                            <div class="fv">{{ $pwd->date_of_birth->format('F d, Y') }} ({{ $pwd->age }} years old)</div>
                        </div>
                        <div>
                            <div class="fl">Sex</div>
                            <div class="fv"><span class="badge {{ $pwd->sex === 'Male' ? 'b-m' : 'b-f' }}">{{ $pwd->sex }}</span></div>
                        </div>
                        <div>
                            <div class="fl">Civil Status</div>
                            <div class="fv">{{ $pwd->civilStatus?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Mobile Number</div>
                            <div class="fv">{{ $pwd->mobile_no ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Email Address</div>
                            <div class="fv">{{ $pwd->email ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Educational Attainment</div>
                            <div class="fv">{{ $pwd->educationalAttainment?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Occupation</div>
                            <div class="fv">{{ $pwd->occupation?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">4Ps Beneficiary</div>
                            <div class="fv">{{ $pwd->is_4ps_beneficiary ? 'Yes' : 'No' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Family Background --}}
            <div class="card ani a3">
                <div class="card-hd">
                    <div class="card-t">Family Background</div>
                </div>
                <div class="mbd">
                    @php $fm = $pwd->familyMembers->keyBy(fn($m) => strtolower($m->relationship)); @endphp
                    @foreach(['Father','Mother','Guardian'] as $rel)
                    @php $member = $fm->get(strtolower($rel)); @endphp
                    <div style="margin-bottom:12px;padding:12px;background:var(--s50);border-radius:9px;border:1px solid var(--s200);">
                        <p style="font-size:10px;font-weight:700;color:var(--s400);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">{{ $rel }}</p>
                        @if($member && ($member->first_name || $member->last_name))
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                            <div>
                                <div class="fl">Last Name</div>
                                <div class="fv">{{ $member->last_name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="fl">First Name</div>
                                <div class="fv">{{ $member->first_name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="fl">Middle Name</div>
                                <div class="fv">{{ $member->middle_name ?? '—' }}</div>
                            </div>
                            @if($member->suffix)
                            <div>
                                <div class="fl">Suffix</div>
                                <div class="fv">{{ $member->suffix }}</div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div style="font-size:12px;color:var(--s400);">Not provided</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Address Information --}}
            <div class="card ani a2">
                <div class="card-hd">
                    <div class="card-t">Address Information</div>
                </div>
                <div class="mbd">
                    @if($pwd->residence)
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                        <div>
                            <div class="fl">House No. & Street</div>
                            <div class="fv">{{ $pwd->residence->house_no_and_street ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="fl">Barangay</div>
                            <div class="fv">{{ $pwd->residence->barangay }}</div>
                        </div>
                        <div>
                            <div class="fl">Municipality</div>
                            <div class="fv">{{ $pwd->residence->municipality }}</div>
                        </div>
                        <div>
                            <div class="fl">Province</div>
                            <div class="fv">{{ $pwd->residence->province }}</div>
                        </div>
                        <div>
                            <div class="fl">Region</div>
                            <div class="fv">{{ $pwd->residence->region }}</div>
                        </div>
                    </div>
                    @else
                    <div class="empty"><div class="empty-t">No address information available</div></div>
                    @endif
                </div>
            </div>

            {{-- Disability Types --}}
            <div class="card ani a2">
                <div class="card-hd">
                    <div class="card-t">Disability Type(s)</div>
                </div>
                <div class="mbd">
                    @if($pwd->disabilities->count() > 0)
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($pwd->disabilities as $disability)
                        <span class="badge">{{ $disability->name }}</span>
                        @endforeach
                    </div>
                    @else
                    <div class="empty"><div class="empty-t">No disability types recorded</div></div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right: Photo and quick actions --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Photo --}}
            <div class="card ani a2">
                <div class="card-hd">
                    <div class="card-t">Photo</div>
                </div>
                <div class="mbd" style="text-align:center;">
                    @if($pwd->photo_path)
                    <img src="{{ asset('storage/' . $pwd->photo_path) }}" alt="PWD Photo" style="max-width:100%;border-radius:8px;">
                    @else
                    <div class="empty"><div class="empty-t">No photo uploaded</div></div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card ani a2">
                <div class="card-hd">
                    <div class="card-t">Quick Stats</div>
                </div>
                <div class="mbd">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;">
                            <div class="fl">Age</div>
                            <div class="fv">{{ $pwd->age }} years</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <div class="fl">Disabilities</div>
                            <div class="fv">{{ $pwd->disabilities->count() }} type(s)</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <div class="fl">Has Photo</div>
                            <div class="fv">{{ $pwd->photo_path ? 'Yes' : 'No' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div style="margin-top:16px;">
        <a href="{{ route('pwd.index') }}" class="btn btn-o">← Back to Registry</a>
    </div>

</div>
@endsection