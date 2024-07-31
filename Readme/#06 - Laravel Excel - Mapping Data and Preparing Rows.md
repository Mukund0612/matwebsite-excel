# Manipulating Data and Mapping Columns in Excel Exports with Laravel Excel

In this section, we will explore how to manipulate data before exporting and how to change the order of columns in Excel sheets using the Laravel Excel package. We will learn about the `WithMapping` interface and the `prepareRows` method to achieve these tasks.

## Prerequisites

Before proceeding, ensure you have a Laravel project set up and have already installed the Laravel Excel package. If not, refer to the earlier sections of this tutorial series for installation and setup instructions.

## Manipulating Data Before Export

Sometimes, you need to manipulate data before exporting it. You may want to append something, remove something, or change the order of the columns in the Excel sheet. Laravel Excel provides several methods to achieve these tasks.

### Step-by-Step Guide

### Step 1: Implement the `WithMapping` Interface

To change the order of columns, we can implement the `WithMapping` interface. This interface requires you to define a `map` method, which returns an array representing a single row of data.

#### Create the Export Class

If you haven't already created the export class, run the following command:

```bash
php artisan make:export UsersMappedExport
```

**Modify the Export Class**

Modify the UsersMappedExport class to implement the FromCollection and WithMapping interfaces:

```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersMappedExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * Retrieve all users from the database.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all();
    }

    /**
     * Map data for each row.
     *
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone_number,
            $user->address,
            $user->created_at->format('Y-m-d'),
        ];
    }

    /**
     * Define headings for the columns.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone Number',
            'Address',
            'Created At',
        ];
    }
}
```
### Step 2: Define the Route for Exporting
Add a route in routes/web.php to trigger the export:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-users-mapped', [ExportsController::class, 'exportMapped']);
```

### Step 3: Define the Controller Method
In your ExportsController, define the exportMapped method to handle the export logic:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\UsersMappedExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportsController extends Controller
{
    public function exportMapped()
    {
        return Excel::download(new UsersMappedExport, 'users_mapped.xlsx');
    }
}
```

### Step 4: Test the Export
Visit the `/export-users-mapped` URL in your browser to download the users_mapped.xlsx file. You will notice that the Excel file now includes the mapped data with customized column order.

### Handling Errors
While mapping, you might encounter errors if you're trying to access properties on an array instead of an object. Make sure your data manipulation aligns with the data structure.

```php
// Correct mapping
public function map($user): array
{
    return [
        $user->id,
        $user->name,
        $user->email,
        $user->phone_number,
        $user->address,
        $user->created_at->format('Y-m-d'),
    ];
}
```

### Step 5: Using the prepareRows Method
The prepareRows method allows you to manipulate data before it's mapped to the export file. This method is called before the data is processed by the map method.

```php
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class UsersMappedExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    // ...

    /**
     * Prepare rows before mapping.
     *
     * @param array $rows
     * @return array
     */
    public function prepareRows($rows): array
    {
        return array_map(function ($user) {
            $user['name'] = strtoupper($user['name']); // Example manipulation
            return $user;
        }, $rows);
    }

    /**
     * Map data for each row.
     *
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user['id'],
            $user['name'],
            $user['email'],
            $user['phone_number'],
            $user['address'],
            $user['created_at'],
        ];
    }
}
```

### Example: Custom Manipulation
Here's an example of how you might manipulate the name field to uppercase before exporting:

```php
public function prepareRows($rows): array
{
    return array_map(function ($user) {
        $user['name'] = strtoupper($user['name']); // Convert name to uppercase
        return $user;
    }, $rows);
}
```

### Step 6: Test the Manipulation
Visit the /export-users-mapped URL again to download the users_mapped.xlsx file. Verify that the name column data is now in uppercase.

## Storing Excel Files on Disk
Apart from downloading, you can also store Excel files on disk using Laravel Excel.

### Storing an Excel File
To store an Excel file on disk, use the store method instead of download. You can specify the storage disk and the file path.

```php
use Illuminate\Support\Facades\Storage;

public function exportMapped()
{
    Excel::store(new UsersMappedExport, 'users/users_mapped.xlsx', 'public');

    return response()->json(['message' => 'File stored successfully.']);
}
```

### Available Export Methods
  * **Download:** Directly download the Excel file to the user's browser.

```php
return Excel::download(new UsersMappedExport, 'users_mapped.xlsx');
```
  * **Store:** Save the Excel file to a specified storage disk.

```php
Excel::store(new UsersMappedExport, 'users/users_mapped.xlsx', 'public');
```
  * **Queue:** Queue the export process for large data exports.

```php
Excel::queue(new UsersMappedExport, 'users_mapped.xlsx');
```

### Configuring Storage Disk
Ensure your config/filesystems.php is set up correctly to handle file storage:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

## Conclusion
In this section, we learned how to manipulate data and map columns for Excel exports using Laravel Excel. We explored the WithMapping interface, prepareRows method, and how to store Excel files on disk. By mastering these techniques, you can create tailored and efficient Excel reports for your Laravel applications.

For more advanced features and configurations, refer to the Laravel Excel Documentation.

This `README.md` file provides a comprehensive guide on how to manipulate data and customize Excel exports using Laravel Excel. It covers the use of the `WithMapping` interface and `prepareRows` method, offering flexibility in data handling and export options.