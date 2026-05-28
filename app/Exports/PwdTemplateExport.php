<?php

namespace App\Exports;

use App\Models\CivilStatus;
use App\Models\DisabilityType;
use App\Models\EducationalAttainment;
use App\Models\Occupation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PwdTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PwdDataSheet(),
            new PwdReferenceSheet(),
        ];
    }
}

// ── Sheet 1: Data entry sheet ─────────────────────────────────────────────────
class PwdDataSheet implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'PWD Data';
    }

    public function array(): array
    {
        return [
            // Row 1: Title
            ['PWD REGISTRY IMPORT TEMPLATE', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],

            // Row 2: Subtitle
            ['Fill in the rows below. Do not change column headers. See the "Reference" sheet for valid values.', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],

            // Row 3: Empty spacer
            [],

            // Row 4: Column headers
            [
                'pwd_number',
                'last_name *',
                'first_name *',
                'middle_name',
                'suffix',
                'date_of_birth *',
                'sex *',
                'civil_status *',
                'disability_types *',
                'educational_attainment *',
                'occupation',
                'mobile_no',
                'email',
                'house_no_and_street',
                'barangay *',
                'municipality *',
                'province *',
                'region *',
            ],

            // Row 5: Example row
            [
                '21000186000',
                'Dela Cruz',
                'Juan',
                'Santos',
                'Jr.',
                '1990-01-15',
                'Male',
                'Single',
                'Visual Disability',
                'College Level',
                'Professionals',
                '09171234567',
                'juan@email.com',
                '123 Rizal St',
                'Poblacion',
                'Tacloban City',
                'Leyte',
                'Region VIII',
            ],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol = 'R';

        // ── Row 1: Title ──────────────────────────────────────────
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1549A8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // ── Row 2: Subtitle ───────────────────────────────────────
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F0FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ── Row 4: Column headers ─────────────────────────────────
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // ── Row 5: Example row ────────────────────────────────────
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF64748B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(20);

        // ── Rows 6–100: Data entry area ───────────────────────────
        $sheet->getStyle("A6:{$lastCol}100")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Zebra striping for data rows
        for ($row = 6; $row <= 100; $row += 2) {
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF1F5F9']],
            ]);
        }

        // Highlight required columns (B, C, F, G, H, I, J, O, P, Q, R)
        foreach (['B', 'C', 'F', 'G', 'H', 'I', 'J', 'O', 'P', 'Q', 'R'] as $col) {
            $sheet->getStyle("{$col}4")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB91C1C']],
            ]);
        }

        // Freeze panes so header stays visible while scrolling
        $sheet->freezePane('A5');

        // Row height for data rows
        for ($row = 6; $row <= 100; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(18);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // pwd_number
            'B' => 16, // last_name
            'C' => 16, // first_name
            'D' => 16, // middle_name
            'E' => 10, // suffix
            'F' => 14, // date_of_birth
            'G' => 10, // sex
            'H' => 18, // civil_status
            'I' => 30, // disability_types
            'J' => 22, // educational_attainment
            'K' => 22, // occupation
            'L' => 15, // mobile_no
            'M' => 24, // email
            'N' => 22, // house_no_and_street
            'O' => 16, // barangay
            'P' => 18, // municipality
            'Q' => 16, // province
            'R' => 14, // region
        ];
    }
}

// ── Sheet 2: Reference sheet ──────────────────────────────────────────────────
class PwdReferenceSheet implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Reference';
    }

    public function array(): array
    {
        $civilStatuses   = CivilStatus::pluck('name')->toArray();
        $educations      = EducationalAttainment::pluck('name')->toArray();
        $occupations     = Occupation::pluck('name')->toArray();
        $disabilities    = DisabilityType::pluck('name')->toArray();

        $rows = [
            ['PWD IMPORT — VALID VALUES REFERENCE', '', '', ''],
            ['Use these exact values in the Data sheet. Copy and paste to avoid typos.', '', '', ''],
            [],
            ['civil_status', 'educational_attainment', 'occupation', 'disability_types'],
        ];

        $max = max(
            count($civilStatuses),
            count($educations),
            count($occupations),
            count($disabilities)
        );

        for ($i = 0; $i < $max; $i++) {
            $rows[] = [
                $civilStatuses[$i]  ?? '',
                $educations[$i]     ?? '',
                $occupations[$i]    ?? '',
                $disabilities[$i]   ?? '',
            ];
        }

        $rows[] = [];
        $rows[] = ['NOTE: disability_types accepts multiple values separated by commas.', '', '', ''];
        $rows[] = ['Example: "Visual Disability, Mental Disability"', '', '', ''];
        $rows[] = [];
        $rows[] = ['sex must be exactly: Male or Female', '', '', ''];
        $rows[] = ['date_of_birth must be in YYYY-MM-DD format', '', '', ''];

        return $rows;
    }

    public function styles(Worksheet $sheet): void
    {
        // Title
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1549A8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Subtitle
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F0FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Column headers row 4
        $sheet->getStyle('A4:D4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // Data rows styling with column colors
        $colColors = ['FFdbeafe', 'FFdcfce7', 'FFfef9c3', 'FFfce7f3'];
        $colLetters = ['A', 'B', 'C', 'D'];

        $highestRow = $sheet->getHighestRow();
        for ($row = 5; $row <= $highestRow; $row++) {
            for ($ci = 0; $ci < 4; $ci++) {
                $col = $colLetters[$ci];
                $val = $sheet->getCell("{$col}{$row}")->getValue();
                if ($val) {
                    $sheet->getStyle("{$col}{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colColors[$ci]]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                        'font' => ['size' => 10],
                    ]);
                }
            }
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // Notes at the bottom
        for ($row = $highestRow - 4; $row <= $highestRow; $row++) {
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF92400E']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFef3c7']],
            ]);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 28,
            'C' => 32,
            'D' => 36,
        ];
    }
}