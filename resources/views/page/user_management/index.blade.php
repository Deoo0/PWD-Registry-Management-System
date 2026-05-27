@extends('layouts.app')
@section('title','User Management')
@section('page-title','User Management')
@section('breadcrumb')<span>›</span><span>Users</span>@endsection

@section('content')
<div class="pg">

    <div class="ph ani a1">
        <div>
            <div class="ph-t">User Management</div>
            <div class="ph-s">System accounts and access control</div>
        </div>
        <button class="btn btn-p" onclick="document.getElementById('mCreate').classList.add('open')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add User
        </button>
    </div>

    {{-- Role summary --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px;" class="ani a2">
        @foreach([
            ['Admin',    'var(--red)',   $adminCount,    'Full system access · can manage users & reports'],
            ['Encoder',  'var(--blue)',  $encoderCount,  'Can register and edit PWD records'],
            ['Approver', 'var(--green)', $approverCount, 'Read-only access to registry and reports'],
        ] as [$label, $col, $cnt, $desc])
        <div class="sc">
            <div class="sc-bar" style="background:{{ $col }};"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div class="sc-v">{{ $cnt }}</div>
                    <div class="sc-l" style="font-weight:600;color:var(--s700);">{{ $label }}{{ $cnt !== 1 ? 's' : '' }}</div>
                    <div style="font-size:11px;color:var(--s400);margin-top:2px;">{{ $desc }}</div>
                </div>
                <div style="width:40px;height:40px;border-radius:10px;background:var(--s50);border:1px solid var(--s200);display:flex;align-items:center;justify-content:center;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="{{ $col }}" stroke-width="1.8" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Users table --}}
    <div class="card ani a3">
        <div class="card-hd">
            <div class="card-t">All Users</div>
            <div style="display:flex;gap:8px;align-items:center;">
                <div class="sw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" class="sinp" id="userSearch" placeholder="Search users…" oninput="filterUsers(this.value)" style="width:180px;">
                </div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="tbl" id="userTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>USN</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr data-name="{{ strtolower($user->first_name.' '.$user->last_name) }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#dc2626);display:flex;align-items:center;justify-content:center;font-size:10.5px;font-weight:800;color:white;flex-shrink:0;">
                                    {{ strtoupper(substr($user->first_name,0,1)) }}{{ strtoupper(substr($user->last_name,0,1)) }}
                                </div>
                                <div>
                                    <div class="cp">{{ $user->last_name }}, {{ $user->first_name }}</div>
                                    <div class="cs">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-family:monospace;font-size:11.5px;color:var(--s500);">{{ $user->usn }}</td>
                        <td>
                            <span class="badge {{ $user->usertype->name === 'Admin' ? 'b-adm' : ($user->usertype->name === 'Encoder' ? 'b-enc' : 'b-apr') }}">
                                {{ $user->usertype->name }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'b-on' : 'b-off' }}">
                                <span class="bd"></span>{{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--s500);">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <button class="btn btn-o btn-sm" onclick="openEdit({{ $user->id }}, '{{ addslashes($user->first_name) }}', '{{ addslashes($user->last_name) }}', '{{ addslashes($user->middle_name ?? '') }}', '{{ addslashes($user->email) }}', '{{ $user->usn }}', {{ $user->usertype_id }})">
                                    Edit
                                </button>
                                @if($user->id !== Auth::id())
                                <form method="POST" action="{{ route('users.toggle', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-d' : 'btn-g' }}">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <div class="empty-t">No users found</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pag">
                {{ $users->links('vendor.pagination.custom') }}
                <span class="pi">{{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
            </div>
        @else
            <div class="pag">
                <span class="pi">{{ $users->total() }} {{ Str::plural('user', $users->total()) }}</span>
            </div>
        @endif
    </div>

</div>

{{-- ── CREATE USER MODAL ── --}}
<div class="mbg" id="mCreate">
    <div class="modal">
        <div class="mhd">
            <div class="mt">Add New User</div>
            <button class="mx" onclick="document.getElementById('mCreate').classList.remove('open')">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <input type="hidden" name="_create_modal" value="1">
            <div class="mbd">
                <div class="g2">
                    <div class="fg">
                        <label class="fl">First Name <span style="color:var(--red)">*</span></label>
                        <input type="text" name="first_name" class="fi @error('first_name') err @enderror" value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                    <div class="fg">
                        <label class="fl">Last Name <span style="color:var(--red)">*</span></label>
                        <input type="text" name="last_name" class="fi @error('last_name') err @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Middle Name</label>
                    <input type="text" name="middle_name" class="fi" value="{{ old('middle_name') }}">
                </div>
                <div class="fg">
                    <label class="fl">Email Address <span style="color:var(--red)">*</span></label>
                    <input type="email" name="email" class="fi @error('email') err @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="fe">{{ $message }}</div>@enderror
                </div>
                <div class="g2">
                    <div class="fg">
                        <label class="fl">USN <span style="color:var(--red)">*</span></label>
                        <input type="text" name="usn" class="fi @error('usn') err @enderror" value="{{ old('usn') }}" placeholder="USN-00000" required>
                        @error('usn')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                    <div class="fg">
                        <label class="fl">Role <span style="color:var(--red)">*</span></label>
                        <select name="usertype_id" class="fsel fi" required>
                            <option value="">Select role…</option>
                            @foreach($usertypes as $ut)
                                <option value="{{ $ut->id }}" {{ old('usertype_id') == $ut->id ? 'selected' : '' }}>{{ $ut->name }}</option>
                            @endforeach
                        </select>
                        @error('usertype_id')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="g2">
                    <div class="fg">
                        <label class="fl">Password <span style="color:var(--red)">*</span></label>
                        <input type="password" name="password" class="fi @error('password') err @enderror" required>
                        @error('password')<div class="fe">{{ $message }}</div>@enderror
                    </div>
                    <div class="fg">
                        <label class="fl">Confirm Password <span style="color:var(--red)">*</span></label>
                        <input type="password" name="password_confirmation" class="fi" required>
                    </div>
                </div>
            </div>
            <div class="mft">
                <button type="button" class="btn btn-o" onclick="document.getElementById('mCreate').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-p">Create User</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT USER MODAL ── --}}
<div class="mbg" id="mEdit">
    <div class="modal">
        <div class="mhd">
            <div class="mt">Edit User</div>
            <button class="mx" onclick="document.getElementById('mEdit').classList.remove('open')">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="mbd">
                <div class="g2">
                    <div class="fg">
                        <label class="fl">First Name <span style="color:var(--red)">*</span></label>
                        <input type="text" name="first_name" id="e_first_name" class="fi" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Last Name <span style="color:var(--red)">*</span></label>
                        <input type="text" name="last_name" id="e_last_name" class="fi" required>
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Middle Name</label>
                    <input type="text" name="middle_name" id="e_middle_name" class="fi">
                </div>
                <div class="fg">
                    <label class="fl">Email Address <span style="color:var(--red)">*</span></label>
                    <input type="email" name="email" id="e_email" class="fi" required>
                </div>
                <div class="g2">
                    <div class="fg">
                        <label class="fl">USN <span style="color:var(--red)">*</span></label>
                        <input type="text" name="usn" id="e_usn" class="fi" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Role <span style="color:var(--red)">*</span></label>
                        <select name="usertype_id" id="e_usertype_id" class="fsel fi" required>
                            @foreach($usertypes as $ut)
                                <option value="{{ $ut->id }}">{{ $ut->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="padding:10px 12px;background:var(--gold-lt);border-radius:8px;border:1px solid #fde68a;font-size:12px;color:#92400e;">
                    Leave password fields blank to keep the current password.
                </div>
                <div class="g2" style="margin-top:12px;">
                    <div class="fg">
                        <label class="fl">New Password</label>
                        <input type="password" name="password" class="fi">
                    </div>
                    <div class="fg">
                        <label class="fl">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="fi">
                    </div>
                </div>
            </div>
            <div class="mft">
                <button type="button" class="btn btn-o" onclick="document.getElementById('mEdit').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-p">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openEdit(id, firstName, lastName, middleName, email, usn, usertypeId) {
    document.getElementById('editForm').action = `/users/${id}`;
    document.getElementById('e_first_name').value  = firstName;
    document.getElementById('e_last_name').value   = lastName;
    document.getElementById('e_middle_name').value = middleName;
    document.getElementById('e_email').value       = email;
    document.getElementById('e_usn').value         = usn;
    document.getElementById('e_usertype_id').value = usertypeId;
    document.getElementById('mEdit').classList.add('open');
}

function filterUsers(q) {
    const rows = document.querySelectorAll('#userTable tbody tr[data-name]');
    rows.forEach(row => {
        row.style.display = row.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}

@if($errors->any() && old('_create_modal'))
    document.getElementById('mCreate').classList.add('open');
@endif
</script>
@endsection