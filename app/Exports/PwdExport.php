<?php

namespace App\Exports;

use App\Models\Pwd;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PwdExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private Request $request) {}

    public function title(): string
    {
        return 'PWD Registry';
    }

    public function query()
    {
        $query = Pwd::with([
            'residence',
            'civilStatus',
            'educationalAttainment',
            'occupation',
            'disabilities',
        ]);

        if ($barangay = $this->request->barangay) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('barangay', 'ilike', "%{$barangay}%")
            );
        }

        if ($municipality = $this->request->municipality) {
            $query->whereHas('residence', fn ($q) =>
                $q->where('municipality', 'ilike', "%{$municipality}%")
            );
        }

        if ($sex = $this->request->sex) {
            $query->where('sex', $sex);
        }

        if ($ageRange = $this->request->age_range) {
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

        if ($disability = $this->request->disability) {
            $query->whereHas('disabilities', fn ($q) =>
                $q->where('name', $disability)
            );
        }

        if ($civilStatus = $this->request->civil_status) {
            $query->whereHas('civilStatus', fn ($q) =>
                $q->where('name', $civilStatus)
            );
        }

        if ($this->request->filled('is_4ps_beneficiary')) {
            $query->where('is_4ps_beneficiary', (bool) $this->request->is_4ps_beneficiary);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'PWD Number',
            'Last Name',
            'First Name',
            'Middle Name',
            'Suffix',
            'Date of Birth',
            'Age',
            'Sex',
            'Civil Status',
            '4Ps Beneficiary',
            'Disability Type(s)',
            'Educational Attainment',
            'Occupation',
            'Mobile No.',
            'Email',
            'House No. & Street',
            'Barangay',
            'Municipality',
            'Province',
            'Region',
            'Date Registered',
        ];
    }

    public function map($pwd): array
    {
        return [
            $pwd->pwd_number ?? '—',
            $pwd->last_name,
            $pwd->first_name,
            $pwd->middle_name ?? '—',
            $pwd->suffix ?? '—',
            $pwd->date_of_birth->format('Y-m-d'),
            $pwd->age,
            $pwd->sex,
            $pwd->civilStatus?->name ?? '—',
            $pwd->is_4ps_beneficiary ? 'Yes' : 'No',
            $pwd->disabilities->pluck('name')->join(', ') ?: '—',
            $pwd->educationalAttainment?->name ?? '—',
            $pwd->occupation?->name ?? '—',
            $pwd->mobile_no ?? '—',
            $pwd->email ?? '—',
            $pwd->residence?->house_no_and_street ?? '—',
            $pwd->residence?->barangay ?? '—',
            $pwd->residence?->municipality ?? '—',
            $pwd->residence?->province ?? '—',
            $pwd->residence?->region ?? '—',
            $pwd->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold and style the header row
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1549A8']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}