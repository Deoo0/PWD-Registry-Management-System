@extends('layouts.app')

{{--
    This single file handles BOTH create and edit.
    In your controller:
        create() → return view('pwd.form')
        edit($pwd) → return view('pwd.form', compact('pwd'))
    $pwd will be null on create, populated on edit.
--}}

@section('title', isset($pwd) ? 'Edit PWD' : 'Register PWD')
@section('page-title', isset($pwd) ? 'Edit PWD Record' : 'Register New PWD')
@section('breadcrumb')
    <span>›</span>
    <a href="{{ route('pwd.index') }}">PWD Registry</a>
    <span>›</span>
    <span>{{ isset($pwd) ? 'Edit' : 'Register' }}</span>
@endsection

@section('content')
<div class="pg">

    {{-- Backend: on edit use route('pwd.update', $pwd) with @method('PUT'), on create use route('pwd.store') --}}
    <form method="POST" action="{{ isset($pwd) ? route('pwd.update', $pwd) : route('pwd.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($pwd)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

            {{-- ── Left: main form ── --}}
            <div style="display:flex;flex-direction:column;gap:14px;">

                {{-- Section 1-3: Application info --}}
                <div class="card ani a1">
                    <div class="card-hd">
                        <div class="card-t">Application Information</div>
                        <div class="card-st">Sections 1–3 of DOH form</div>
                    </div>
                    <div class="mbd">
                        <div class="g3">
                            <div class="fg">
                                <label class="fl">PWD Number</label>
                                <input type="text" name="pwd_number" class="fi @error('pwd_number') err @enderror"
                                    placeholder="RR-PPMM-BBB-NNNNNNNN"
                                    value="{{ old('pwd_number', $pwd->pwd_number ?? '') }}">
                                @error('pwd_number')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4-6: Personal info --}}
                <div class="card ani a2">
                    <div class="card-hd">
                        <div class="card-t">Personal Information</div>
                        <div class="card-st">Sections 4–6 of DOH form</div>
                    </div>
                    <div class="mbd">
                        <div class="g3">
                            <div class="fg">
                                <label class="fl">Last Name <span style="color:var(--red)">*</span></label>
                                <input type="text" name="last_name" class="fi @error('last_name') err @enderror"
                                    value="{{ old('last_name', $pwd->last_name ?? '') }}" required>
                                @error('last_name')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">First Name <span style="color:var(--red)">*</span></label>
                                <input type="text" name="first_name" class="fi @error('first_name') err @enderror"
                                    value="{{ old('first_name', $pwd->first_name ?? '') }}" required>
                                @error('first_name')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">Middle Name</label>
                                <input type="text" name="middle_name" class="fi"
                                    value="{{ old('middle_name', $pwd->middle_name ?? '') }}">
                            </div>
                        </div>
                        <div class="g3">
                            <div class="fg">
                                <label class="fl">Suffix</label>
                                <input type="text" name="suffix" class="fi" placeholder="Jr., Sr., III…"
                                    value="{{ old('suffix', $pwd->suffix ?? '') }}">
                            </div>
                            <div class="fg">
                                <label class="fl">Date of Birth <span style="color:var(--red)">*</span></label>
                                <input type="date" name="date_of_birth" class="fi @error('date_of_birth') err @enderror"
                                    value="{{ old('date_of_birth', isset($pwd) ? $pwd->date_of_birth->format('Y-m-d') : '') }}" required>
                                @error('date_of_birth')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">Sex <span style="color:var(--red)">*</span></label>
                                <select name="sex" class="fsel fi" required>
                                    <option value="">Select…</option>
                                    <option {{ old('sex', $pwd->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option {{ old('sex', $pwd->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('sex')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="g3">
                            <div class="fg">
                                {{-- Backend: CivilStatus::all() --}}
                                <label class="fl">Civil Status <span style="color:var(--red)">*</span></label>
                                <select name="civil_status_id" class="fsel fi" required>
                                    <option value="">Select…</option>
                                    @foreach(\App\Models\CivilStatus::all() as $cs)
                                        <option value="{{ $cs->id }}" {{ old('civil_status_id', $pwd->civil_status_id ?? '') == $cs->id ? 'selected' : '' }}>{{ $cs->name }}</option>
                                    @endforeach
                                </select>
                                @error('civil_status_id')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">Mobile Number</label>
                                <input type="text" name="mobile_no" class="fi" placeholder="09XXXXXXXXX"
                                    value="{{ old('mobile_no', $pwd->mobile_no ?? '') }}">
                            </div>
                            <div class="fg">
                                <label class="fl">Email Address</label>
                                <input type="email" name="email" class="fi"
                                    value="{{ old('email', $pwd->email ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 8: Disability types --}}
                <div class="card ani a2">
                    <div class="card-hd">
                        <div class="card-t">Type of Disability</div>
                        <div class="card-st">Section 8 — select all that apply</div>
                    </div>
                    <div class="mbd">
                        {{--
                            Backend: get selected IDs with:
                            $selectedDisabilities = isset($pwd)
                                ? $pwd->latestApplication?->disabilities->pluck('id')->toArray()
                                : [];
                        --}}
                        @php
                            $disabilityTypes = \App\Models\DisabilityType::all();
                            $selectedDisabilities = old('disability_types',
                                isset($pwd) ? ($pwd->pluck('disability_type_id')->toArray() ?? []) : []
                            );
                        @endphp
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            @foreach($disabilityTypes as $dt)
                            <label style="display:flex;align-items:center;gap:8px;padding:9px 11px;border-radius:8px;border:1px solid {{ in_array($dt->id, $selectedDisabilities) ? 'var(--blue)' : 'var(--s200)' }};cursor:pointer;background:{{ in_array($dt->id, $selectedDisabilities) ? 'var(--blue-xlt)' : 'white' }};transition:all .14s;" onclick="toggleDisability(this)">
                                <input type="checkbox" name="disability_types[]" value="{{ $dt->id }}"
                                    {{ in_array($dt->id, $selectedDisabilities) ? 'checked' : '' }}
                                    style="accent-color:var(--blue);width:14px;height:14px;flex-shrink:0;">
                                <span style="font-size:12.5px;color:var(--s700);">{{ $dt->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('disability_types')<div class="fe" style="margin-top:6px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Section 10: Residence --}}
                <div class="card ani a3">
                    <div class="card-hd">
                        <div class="card-t">Residence Address</div>
                        <div class="card-st">Section 10 of DOH form</div>
                    </div>
                    <div class="mbd">
                        <div class="fg">
                            <label class="fl">House No. and Street</label>
                            <input type="text" name="house_no_and_street" class="fi"
                                value="{{ old('house_no_and_street', $pwd->residence?->house_no_and_street ?? '') }}">
                        </div>
                        <div class="g2">
                            <div class="fg">
                                <label class="fl">Barangay <span style="color:var(--red)">*</span></label>
                                <input type="text" name="barangay" class="fi @error('barangay') err @enderror" required
                                    value="{{ old('barangay', $pwd->residence?->barangay ?? '') }}">
                                @error('barangay')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">Municipality <span style="color:var(--red)">*</span></label>
                                <input type="text" name="municipality" class="fi @error('municipality') err @enderror" required
                                    value="{{ old('municipality', $pwd->residence?->municipality ?? '') }}">
                                @error('municipality')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="g2">
                            <div class="fg">
                                <label class="fl">Province <span style="color:var(--red)">*</span></label>
                                <input type="text" name="province" class="fi @error('province') err @enderror" required
                                    value="{{ old('province', $pwd->residence?->province ?? '') }}">
                                @error('province')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                <label class="fl">Region <span style="color:var(--red)">*</span></label>
                                <input type="text" name="region" class="fi @error('region') err @enderror" required
                                    value="{{ old('region', $pwd->residence?->region ?? '') }}">
                                @error('region')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sections 12, 14: Education + Occupation --}}
                <div class="card ani a3">
                    <div class="card-hd">
                        <div class="card-t">Education &amp; Occupation</div>
                        <div class="card-st">Sections 12 &amp; 14 of DOH form</div>
                    </div>
                    <div class="mbd">
                        <div class="g2">
                            <div class="fg">
                                {{-- Backend: EducationalAttainment::all() --}}
                                <label class="fl">Educational Attainment <span style="color:var(--red)">*</span></label>
                                <select name="educational_attainment_id" class="fsel fi" required>
                                    <option value="">Select…</option>
                                    @foreach(\App\Models\EducationalAttainment::all() as $ea)
                                        <option value="{{ $ea->id }}" {{ old('educational_attainment_id', $pwd->educational_attainment_id ?? '') == $ea->id ? 'selected' : '' }}>{{ $ea->name }}</option>
                                    @endforeach
                                </select>
                                @error('educational_attainment_id')<div class="fe">{{ $message }}</div>@enderror
                            </div>
                            <div class="fg">
                                {{-- Backend: Occupation::all() --}}
                                <label class="fl">Occupation</label>
                                <select name="occupation_id" class="fsel fi">
                                    <option value="">Select…</option>
                                    @foreach(\App\Models\Occupation::all() as $occ)
                                        <option value="{{ $occ->id }}" {{ old('occupation_id', $pwd->occupation_id ?? '') == $occ->id ? 'selected' : '' }}>{{ $occ->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 17: Family Background --}}
                <div class="card ani a4">
                    <div class="card-hd">
                        <div class="card-t">Family Background</div>
                        <div class="card-st">Section 17 — Father, Mother, Guardian</div>
                    </div>
                    <div class="mbd">
                        {{--
                            Backend: pass $familyMembers = isset($pwd)
                                ? $pwd->latestApplication?->familyMembers->keyBy('relationship')
                                : collect();
                        --}}
                        @php
                            $fm = isset($pwd) ? ($pwd->latestApplication?->familyMembers->keyBy('relationship') ?? collect()) : collect();
                        @endphp
                        @foreach(['Father','Mother','Guardian'] as $rel)
                        <div style="margin-bottom:14px;padding:12px;background:var(--s50);border-radius:9px;border:1px solid var(--s200);">
                            <p style="font-size:10px;font-weight:700;color:var(--s400);text-transform:uppercase;letter-spacing:.1em;margin-bottom:9px;">{{ $rel }}</p>
                            <div class="g3">
                                <div class="fg" style="margin-bottom:0;">
                                    <label class="fl">Last Name</label>
                                    <input type="text" name="family[{{ strtolower($rel) }}][last_name]" class="fi"
                                        value="{{ old('family.'.strtolower($rel).'.last_name', $fm->get($rel)?->last_name ?? '') }}">
                                </div>
                                <div class="fg" style="margin-bottom:0;">
                                    <label class="fl">First Name</label>
                                    <input type="text" name="family[{{ strtolower($rel) }}][first_name]" class="fi"
                                        value="{{ old('family.'.strtolower($rel).'.first_name', $fm->get($rel)?->first_name ?? '') }}">
                                </div>
                                <div class="fg" style="margin-bottom:0;">
                                    <label class="fl">Middle Name</label>
                                    <input type="text" name="family[{{ strtolower($rel) }}][middle_name]" class="fi"
                                        value="{{ old('family.'.strtolower($rel).'.middle_name', $fm->get($rel)?->middle_name ?? '') }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>{{-- /left --}}

            {{-- ── Right: photo + summary ── --}}
            <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:calc(var(--th) + 22px);">

                {{-- Photo upload --}}
                <div class="card ani a1">
                    <div class="card-hd"><div class="card-t">Photo</div></div>
                    <div class="mbd" style="text-align:center;">
                        <div id="photo-preview" style="width:110px;height:130px;border-radius:9px;border:2px dashed var(--s300);margin:0 auto 12px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:var(--s50);cursor:pointer;" onclick="document.getElementById('photo-input').click()">
                            @if(isset($pwd) && $pwd->latestApplication?->photo_path)
                                <img src="{{ asset('storage/'.$pwd->latestApplication->photo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div style="text-align:center;color:var(--s400);">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:28px;height:28px;margin:0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p style="font-size:10px;margin-top:4px;">1×1 Photo</p>
                                </div>
                            @endif
                        </div>
                        <input type="file" id="photo-input" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                        <button type="button" class="btn btn-o btn-sm" onclick="document.getElementById('photo-input').click()" style="font-size:11.5px;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Upload Photo
                        </button>
                        <p style="font-size:10px;color:var(--s400);margin-top:6px;">JPG, PNG · Max 2MB</p>
                    </div>
                </div>

                {{-- Form summary / notes --}}
                <div class="card ani a2">
                    <div class="card-hd"><div class="card-t">Summary</div></div>
                    <div style="padding:14px 16px;">
                        <div style="font-size:11.5px;color:var(--s500);line-height:1.7;">
                            <p>Fields marked <span style="color:var(--red);font-weight:700;">*</span> are required.</p>
                            <p style="margin-top:6px;">Based on <strong>DOH Philippine Registry for PWDs Version 4.0</strong>, revised August 2021.</p>
                            @if(isset($pwd))
                            <div style="margin-top:10px;padding:10px;background:var(--gold-lt);border-radius:7px;border:1px solid #fde68a;">
                                <p style="font-size:10.5px;font-weight:700;color:#92400e;">Editing existing record</p>
                                <p style="font-size:10.5px;color:#92400e;margin-top:2px;">PWD: {{ $pwd->full_name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card ani a3">
                    <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
                        <button type="submit" class="btn btn-p" style="justify-content:center;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ isset($pwd) ? 'Update Record' : 'Register PWD' }}
                        </button>
                        <a href="{{ route('pwd.index') }}" class="btn btn-o" style="justify-content:center;">
                            Cancel
                        </a>
                        @if(isset($pwd))
                        <div style="height:1px;background:var(--s100);margin:2px 0;"></div>
                        <form method="POST" action="{{ route('pwd.destroy', $pwd) }}" onsubmit="return confirm('Delete this PWD record? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-d btn-sm" style="width:100%;justify-content:center;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete Record
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

            </div>{{-- /right --}}
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleDisability(label) {
    const cb = label.querySelector('input[type=checkbox]');
    if (cb.checked) {
        label.style.borderColor = 'var(--blue)';
        label.style.background = 'var(--blue-xlt)';
    } else {
        label.style.borderColor = 'var(--s200)';
        label.style.background = 'white';
    }
}
</script>
@endsection