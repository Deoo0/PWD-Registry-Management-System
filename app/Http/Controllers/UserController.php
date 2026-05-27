<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usertype;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users         = User::with('usertype')->latest()->paginate(15);
        $usertypes     = Usertype::orderBy('name')->get();
        $adminCount    = User::where('usertype_id', 1)->count();
        $encoderCount  = User::where('usertype_id', 2)->count();
        $approverCount = User::where('usertype_id', 3)->count();

        return view('page.user_management.index', compact(
            'users', 'usertypes', 'adminCount', 'encoderCount', 'approverCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'username'         => ['required', 'string', 'max:50', 'unique:users,username'],
            'usertype_id' => ['required', 'exists:usertype,id'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'email'       => $validated['email'],
            'username'     => $validated['username'],
            'usertype_id' => $validated['usertype_id'],
            'password'    => Hash::make($validated['password']),
            'is_active'   => true,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email'       => ['required', 'email', 'unique:users,email,' . $user->id],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'usertype_id' => ['required', 'exists:usertype,id'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? "",
            'email'       => $validated['email'],
            'username'         => $validated['username'],
            'usertype_id' => $validated['usertype_id'],
            ...($validated['password']
                ? ['password' => Hash::make($validated['password'])]
                : []),
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}