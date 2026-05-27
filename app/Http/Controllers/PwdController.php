<?php

namespace App\Http\Controllers;

use App\Models\Pwd;
use App\Models\PwdDisability;
use App\Models\DisabilityType;
use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PwdController extends Controller
{
    /**
     * List all registered PWDs with optional filters.
     *
     * Query params: search, sex, barangay, age_range, disability
     */
    public function index(Request $request): View
    {
        $query = Pwd::with([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name',  'ilike', "%{$search}%")
                  ->orWhere('last_name',  'ilike', "%{$search}%")
                  ->orWhere('pwd_number', 'ilike', "%{$search}%");
            });
        }

        if ($sex = $request->sex) {
            $query->where('sex', $sex);
        }

        if ($barangay = $request->barangay) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('barangay', 'ilike', "%{$barangay}%")
            );
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

        $pwds = $query->latest()->paginate(15)->withQueryString();

        $disabilityTypes = DisabilityType::orderBy('name')->get();

        $barangays = Residence::distinct()->orderBy('barangay')->pluck('barangay');

        $disabilityStats = DisabilityType::withCount('pwds')  // requires a pwds() relationship on DisabilityType
            ->orderBy('pwds_count', 'desc')
            ->get()
            ->map(fn ($t) => (object)['name' => $t->name, 'total' => $t->pwds_count]);

        return view('page.pwd.index', compact('pwds', 'disabilityTypes', 'barangays', 'disabilityStats'));
    }

    /**
     * Show the registration form.
     */
    public function pwdCreate(): View
    {
        return view('page.pwd.form');
    }

    /**
     * Store a new PWD record.
     */
    public function pwdStore(Request $request): RedirectResponse
    {
        $validated = $this->validatePwd($request);

        DB::transaction(function () use ($request, $validated) {

            $residence = Residence::create([
                'house_no_and_street' => $validated['house_no_and_street'] ?? null,
                'barangay'            => $validated['barangay'],
                'municipality'        => $validated['municipality'],
                'province'            => $validated['province'],
                'region'              => $validated['region'],
            ]);

            $pwd = Pwd::create([
                'last_name'                  => $validated['last_name'],
                'first_name'                 => $validated['first_name'],
                'middle_name'                => $validated['middle_name'] ?? null,
                'suffix'                     => $validated['suffix'] ?? null,
                'date_of_birth'              => $validated['date_of_birth'],
                'sex'                        => $validated['sex'],
                'civil_status_id'            => $validated['civil_status_id'],
                'educational_attainment_id'  => $validated['educational_attainment_id'],
                'occupation_id'              => $validated['occupation_id'] ?? null,
                'mobile_no'                  => $validated['mobile_no'] ?? null,
                'email'                      => $validated['email'] ?? null,
                'pwd_number'                 => $validated['pwd_number'] ?? null,
                'residence_id'               => $residence->id,
            ]);

            if ($request->hasFile('photo')) {
                $pwd->update([
                    'photo_path' => $request->file('photo')->store('pwd-photos', 'public'),
                ]);
            }

            if (!empty($validated['disability_types'])) {
                // sync() handles inserts cleanly on a many-to-many
                $pwd->disabilities()->sync($validated['disability_types']);
            }
        });

        return redirect()
            ->route('pwd.index')
            ->with('success', 'PWD successfully registered.');
    }

    /**
     * Show a single PWD record.
     */
    public function pwdShow(Pwd $pwd): View
    {
        $pwd->load([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        return view('page.pwd.show', compact('pwd'));
    }

    /**
     * Show the edit form for an existing PWD.
     */
    public function pwdEdit(Pwd $pwd): View
    {
        $pwd->load([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        return view('page.pwd.form', compact('pwd'));
    }

    /**
     * Update an existing PWD record.
     */
    public function update(Request $request, Pwd $pwd): RedirectResponse
    {
        $validated = $this->validatePwd($request, $pwd->id);

        DB::transaction(function () use ($request, $validated, $pwd) {

            if ($pwd->residence) {
                $pwd->residence->update([
                    'house_no_and_street' => $validated['house_no_and_street'] ?? null,
                    'barangay'            => $validated['barangay'],
                    'municipality'        => $validated['municipality'],
                    'province'            => $validated['province'],
                    'region'              => $validated['region'],
                ]);
            } else {
                $residence = Residence::create([
                    'house_no_and_street' => $validated['house_no_and_street'] ?? null,
                    'barangay'            => $validated['barangay'],
                    'municipality'        => $validated['municipality'],
                    'province'            => $validated['province'],
                    'region'              => $validated['region'],
                ]);
                $pwd->residence_id = $residence->id;
            }

            $pwd->update([
                'last_name'                  => $validated['last_name'],
                'first_name'                 => $validated['first_name'],
                'middle_name'                => $validated['middle_name'] ?? null,
                'suffix'                     => $validated['suffix'] ?? null,
                'date_of_birth'              => $validated['date_of_birth'],
                'sex'                        => $validated['sex'],
                'civil_status_id'            => $validated['civil_status_id'],
                'educational_attainment_id'  => $validated['educational_attainment_id'],
                'occupation_id'              => $validated['occupation_id'] ?? null,
                'mobile_no'                  => $validated['mobile_no'] ?? null,
                'email'                      => $validated['email'] ?? null,
                'pwd_number'                 => $validated['pwd_number'] ?? null,
            ]);

            if ($request->hasFile('photo')) {
                $pwd->update([
                    'photo_path' => $request->file('photo')->store('pwd-photos', 'public'),
                ]);
            }

            // sync() replaces old disability links with the new set in one call
            $pwd->disabilities()->sync($validated['disability_types'] ?? []);
        });

        return redirect()
            ->route('pwd.show', $pwd)
            ->with('success', 'PWD record updated successfully.');
    }

    /**
     * Soft delete a PWD record.
     */
    public function pwdDestroy(Pwd $pwd): RedirectResponse
    {
        $pwd->disabilities()->detach(); // clean up pivot rows
        $pwd->delete();

        return redirect()
            ->route('pwd.index')
            ->with('success', 'PWD record has been deleted.');
    }

    /**
     * Shared validation rules for store and update.
     */
    private function validatePwd(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'last_name'                 => ['required', 'string', 'max:100'],
            'first_name'                => ['required', 'string', 'max:100'],
            'middle_name'               => ['nullable', 'string', 'max:100'],
            'suffix'                    => ['nullable', 'string', 'max:20'],
            'date_of_birth'             => ['required', 'date', 'before:today'],
            'sex'                       => ['required', 'in:Male,Female'],
            'civil_status_id'           => ['required', 'exists:civil_status,id'],
            'educational_attainment_id' => ['required', 'exists:educational_attainments,id'],
            'occupation_id'             => ['nullable', 'exists:occupations,id'],
            'mobile_no'                 => ['nullable', 'string', 'max:20'],
            'email'                     => ['nullable', 'email', 'max:255'],
            'disability_types'          => ['required', 'array', 'min:1'],
            'disability_types.*'        => ['exists:disability_type,id'],
            'house_no_and_street'       => ['nullable', 'string', 'max:255'],
            'barangay'                  => ['required', 'string', 'max:100'],
            'municipality'              => ['required', 'string', 'max:100'],
            'province'                  => ['required', 'string', 'max:100'],
            'region'                    => ['required', 'string', 'max:100'],
            'photo'                     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'disability_types.required' => 'Please select at least one disability type.',
            'disability_types.min'      => 'Please select at least one disability type.',
            'date_of_birth.before'      => 'Date of birth must be in the past.',
            'pwd_number.unique'         => 'This PWD number is already registered.',
        ]);
    }
}