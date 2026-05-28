<?php

namespace App\Imports;

use App\Models\CivilStatus;
use App\Models\DisabilityType;
use App\Models\EducationalAttainment;
use App\Models\Occupation;
use App\Models\Pwd;
use App\Models\Residence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class PwdImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public int $imported      = 0;
    public int $skipped       = 0;
    public array $rowErrors   = []; // renamed to avoid conflict with SkipsErrors::$errors

    public function collection(Collection $rows)
    {
        $civilStatuses   = CivilStatus::pluck('id', 'name');
        $educations      = EducationalAttainment::pluck('id', 'name');
        $occupations     = Occupation::pluck('id', 'name');
        $disabilityTypes = DisabilityType::pluck('id', 'name');

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            try {
                if (empty($row['last_name']) || empty($row['first_name']) || empty($row['date_of_birth']) || empty($row['sex'])) {
                    $this->rowErrors[] = "Row {$rowNum}: Missing required fields (last_name, first_name, date_of_birth, sex).";
                    $this->skipped++;
                    continue;
                }

                $civilStatusId = $civilStatuses->get($row['civil_status'] ?? '') ?? null;
                $educationId   = $educations->get($row['educational_attainment'] ?? '') ?? null;
                $occupationId  = $occupations->get($row['occupation'] ?? '') ?? null;

                if (!$civilStatusId) {
                    $this->rowErrors[] = "Row {$rowNum}: Civil status '{$row['civil_status']}' not found.";
                    $this->skipped++;
                    continue;
                }

                if (!$educationId) {
                    $this->rowErrors[] = "Row {$rowNum}: Educational attainment '{$row['educational_attainment']}' not found.";
                    $this->skipped++;
                    continue;
                }

                DB::transaction(function () use ($row, $civilStatusId, $educationId, $occupationId, $disabilityTypes) {

                    $residence = Residence::create([
                        'house_no_and_street' => $row['house_no_and_street'] ?? null,
                        'barangay'            => $row['barangay'] ?? '',
                        'municipality'        => $row['municipality'] ?? '',
                        'province'            => $row['province'] ?? '',
                        'region'              => $row['region'] ?? '',
                    ]);

                    $pwd = Pwd::create([
                        'last_name'                  => $row['last_name'],
                        'first_name'                 => $row['first_name'],
                        'middle_name'                => $row['middle_name'] ?? null,
                        'suffix'                     => $row['suffix'] ?? null,
                        'date_of_birth'              => \Carbon\Carbon::parse($row['date_of_birth'])->format('Y-m-d'),
                        'sex'                        => $row['sex'],
                        'civil_status_id'            => $civilStatusId,
                        'educational_attainment_id'  => $educationId,
                        'occupation_id'              => $occupationId,
                        'mobile_no'                  => $row['mobile_no'] ?? null,
                        'email'                      => $row['email'] ?? null,
                        'pwd_number'                 => $row['pwd_number'] ?? null,
                        'residence_id'               => $residence->id,
                    ]);

                    if (!empty($row['disability_types'])) {
                        $names = array_map('trim', explode(',', $row['disability_types']));
                        $ids   = $disabilityTypes->filter(fn ($id, $name) => in_array($name, $names))->values();
                        $pwd->disabilities()->sync($ids);
                    }
                });

                $this->imported++;

            } catch (\Throwable $e) {
                $this->rowErrors[] = "Row {$rowNum}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }
}