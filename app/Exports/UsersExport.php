<?php

namespace App\Exports;

use Throwable;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Psy\CodeCleaner\ReturnTypePass;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\BeforeWriting;

// class UsersExport implements WithMultipleSheets, ShouldQueue
class UsersExport implements FromArray, ShouldQueue, WithEvents
{
    use Exportable, Queueable, RegistersEventListeners;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data->toArray();
    }

    /* public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $user) {
            $sheets[] = new UserSheetExport($user);
        }
        return $sheets;
    } */

    public function failed(Throwable $exception)
    {
        //
    }

    public static function beforeExport(BeforeExport $event)
    {
    }
    public static function beforeWriting(BeforeWriting $event)
    {
    }
    public static function beforeSheet(BeforeSheet $event)
    {
    }
    public static function afterSheet(AfterSheet $event)
    {
    }
}
