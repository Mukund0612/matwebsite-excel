# Adding Headings to Excel Exports with Laravel Excel

In this section, we will explore how to include headings in your Excel exports using the Laravel Excel package. We will see how to use the `WithHeadings` interface to define custom headings, manage multiple heading rows, and customize the order of columns.

## Prerequisites

Before proceeding, ensure you have a Laravel project set up and have already installed the Laravel Excel package. If not, refer to the earlier sections of this tutorial series for installation and setup instructions.

## Adding Headings to Excel Exports

By default, when exporting data as arrays or collections, the Laravel Excel package does not include column headings. However, we can easily add them using the `WithHeadings` interface.

### Step-by-Step Guide

### Step 1: Implement the `FromArray` Interface

First, we need to clean up our export file and ensure it implements the `FromArray` interface, as most of the time, you will work with arrays of data or collections.

#### Create the Export Class

If you haven't already created the export class, run the following command:

```bash
php artisan make:export UsersArrayExport
```
**Modify the Export Class**

Modify the UsersArrayExport class to implement the FromArray and WithHeadings interfaces. We will also define the headings array:

```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersArrayExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Created At',
            'Updated At',
        ];
    }
}
```

### Step 2: Remove Unwanted Columns
To remove unwanted columns such as timestamps, ensure that your export data does not include those fields. You can achieve this by filtering the array in the controller or during the data preparation.

```php
$data = User::all(['id', 'name', 'email'])->toArray(); // Exclude timestamps
```

### Step 3: Create a Route for Exporting
Add a route in routes/web.php to trigger the export:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-users', [ExportsController::class, 'export']);
```

### Step 4: Define the Controller Method
In your ExportsController, define the export method to handle the export logic:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\UsersArrayExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\User;

class ExportsController extends Controller
{
    public function export()
    {
        $data = User::all(['id', 'name', 'email'])->toArray(); // Prepare user data

        return Excel::download(new UsersArrayExport($data), 'users_with_headings.xlsx');
    }
}
```

### Step 5: Test the Export
Visit the `/export-users` URL in your browser to download the users_with_headings.xlsx file. You will notice that the Excel file now includes a header row with the specified headings.

## Adding Multiple Heading Rows
If you need more than one heading row, you can do so by returning an array of arrays from the `headings` method. Each internal array represents a separate heading row.

```php
public function headings(): array
{
    return [
        ['Users Export'], // First heading row
        ['ID', 'Name', 'Email', 'Created At', 'Updated At'] // Second heading row
    ];
}
```

## Customizing Headings with Constant Arrays
If you have multiple exports of the same model and want to avoid duplicating heading definitions, you can map database columns to user-friendly names using a constant array in your model.

### Define a Constant Array in the Model
In your User model, define a constant array to map column names to custom field names:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    const FIELD_NAMES = [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];
}
```

### Use the Constant in the Export Class
In your export class, you can grab the keys of the data and map them to the custom field names:

```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersArrayExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_values(User::FIELD_NAMES);
    }
}
```

## Mapping Column Order
Currently, our headings are based on the order of columns in the data. However, you can customize the column order by rearranging the keys in the data preparation step.

### Example: Custom Column Order
```php
$data = User::all(['name', 'email', 'id'])->toArray(); // Custom column order
```
In this example, the column order is set to Name, Email, ID.

### Conclusion
In this section, we explored various ways to include headings in Excel exports using Laravel Excel. We learned how to use the WithHeadings interface to define headings, manage multiple heading rows, and customize the order of columns. We also saw how to use constant arrays in models to reduce code duplication.

By implementing these techniques, you can create more user-friendly and readable Excel exports tailored to your application's needs. For more information and advanced features, refer to the Laravel Excel Documentation.

This `README.md` file provides a comprehensive guide on how to include headings in Excel exports using Laravel Excel. It covers the use of the `WithHeadings` interface, multiple heading rows, and customization techniques to make your exports more user-friendly and flexible.