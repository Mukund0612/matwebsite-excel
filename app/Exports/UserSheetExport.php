<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserSheetExport implements WithTitle, WithHeadings, FromQuery, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles
{
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->user->name;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'id',
            'name',
            'email'
        ];
    }

    /**
     * @return Collection
     */
    public function query()
    {
        return User::where('id', $this->user->id);
    }

    /**
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['id'],
            $row['name'],
            $row['email']
        ];
    }

    /**
     * set custom width to columns (Width of cells like A for id, B for name, C for email) with passing numeric value
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'C' => 10
        ];
    }

    /**
     * Style rows
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        /* return [
            1    => ['font' => ['bold' => true]],
        ]; */

        $sheet->getStyle('1')->getFont()->setBold(true);

        // add wraping text
        $sheet->getStyle('C1:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
    }
}
