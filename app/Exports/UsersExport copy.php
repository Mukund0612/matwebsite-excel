<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UsersExport implements ShouldQueue, FromArray, WithHeadings, WithMapping/* , Responsable */, WithStrictNullComparison
{
    use Exportable, Queueable;

    protected $data;

    // /**
    //  * It's required to define the fileName within
    //  * the export class when making use of Responsable.
    //  */
    // private $fileName = 'users.xlsx';

    // /**
    //  * Optional Writer Type
    //  */
    // private $writerType = Excel::XLSX;

    // /**
    //  * Optional Headers
    //  */
    // private $headers = [
    //     'Content-Type' => 'text/csv',
    // ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * its must required when we use WithHeadings
     * @return array
     */
    public function headings(): array
    {
        // single heading row
        /* return [
            'ID #', 
            'Name', 
            'Email'
        ]; */

        // multiple heading row
        /* return [
            [
                'ID #',
                'Name',
                'Email'
            ],
            [
                'ID #1',
                'Name1',
                'Email1'
            ]
        ]; */

        // get heading from model constant ( which is defind in model )
        $headings = [];

        foreach ($this->data->toArray()[0] as $key => $value) {
            $headings[] = User::HEADINGS[$key];
        }

        $headings[] = 'Failed Users Count';

        return $headings;
    }

    // /**
    //  * @return \Illuminate\Support\Collection
    //  */
    // public function collection()
    // {
    //     return User::all();
    // }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data->toArray();
    }

    /**
     * Before mapping the data
     * Prepare the rows by appending '{prepared}' to the name of each user.
     *
     * @param \Illuminate\Support\Collection $rows The collection of user objects.
     * @return \Illuminate\Support\Collection The collection of user objects with the name modified.
     */
    public function prepareRows($rows)
    {
        // transform method is not working with array ( its working with object )
        /* return $rows->transform(function ($user) {
            $user->name .= ' {prepared}';

            return $user;
        }); */

        // prepare data with useing map method
        /* return array_map(function ($user) {
            $user['name'] .= ' {prepared}';

            return $user;
        }, $rows); */

        // prepare data using foreach method
        foreach ($rows as $key => $user) {
            $rows[$key]['name'] .= ' {prepared}';
        }

        return $rows;
    }

    // /**
    //  * @return Builder|EloquentBuilder|Relation|ScoutBuilder
    //  */
    // public function query()
    // {
    //     return User::where('id', '>', 100);
    // }

    // /**
    //  * @return View
    //  */
    // public function view(): View
    // {
    //     return view('users', [
    //         'data' => $this->data
    //     ]);
    // }

    /**
     * Map data for each single row
     * @param  RowType  $row
     * @return array
     */
    public function map($row): array
    {
        // single row
        return [
            $row['id'],
            $row['name'],
            $row['email'],
            User::where('email', 'delta.botsford@example.org')->where('id', $row['id'])->count()
        ];

        // multiple row
        /* return [
            [
                $row['id'],
                $row['email'],
                $row['name']
            ],
            [
                $row['id'],
                $row['email'],
                '----------'
            ],
            [
                $row['id'],
                '----------',
                $row['email']
            ]
        ]; */
    }
}
