<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportsController extends Controller
{
    public function export()
    {
        $users = User::all();

        // for download directly
        // return Excel::download(new UsersExport($users), 'users.xlsx');

        // for store excel file in storage or buckets
        // return Excel::store(new UsersExport($users), 'users.xlsx');

        // for store excel file using Exportable Concerns :: STORE
        // return (new UsersExport($users))->store('users.xlsx');

        // for download excel file using Exportable Concerns :: DOWNLOAD
        // return (new UsersExport($users))->download('users.xlsx');

        // if not definde Responsible in Exports instance
        // return (new UsersExport($users))->download('users.xlsx', Excel::XLSX);

        // define Responsible 
        // return new UsersExport($users);

        // export throw queue
        (new UsersExport($users))->store('users.xlsx');
        return 'Exporting...';
    }
}
