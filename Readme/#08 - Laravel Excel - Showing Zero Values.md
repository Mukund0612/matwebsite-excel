# Handling Empty Cells and Exporting Multiple Sheets with Laravel Excel

In this section, we'll explore a common issue you might face when using Laravel Excel for the first time, especially when dealing with integer values. We'll also learn how to export Excel files with multiple sheets and customize each cell.

## Handling Empty Cells in Exports

When exporting data with Laravel Excel, you may encounter a situation where cells that should display `0` appear empty instead. This is because Laravel Excel, by default, compares only the values of the cells and not their types. Consequently, `0` is treated as equivalent to an empty string. We can solve this issue by using the `WithStrictNullComparison` interface.

### Step-by-Step Guide

### Step 1: Modify the Export Class

Let's start by adding a new column to display the count of failed orders for each user. We'll update the `headings` and `map` methods to include this new column.

#### Create the Export Class

If you haven't already created the export class, run the following command:

```bash
php artisan make:export UsersWithFailedOrdersExport
```

**Modify the Export Class**
Modify the UsersWithFailedOrdersExport class to include the count of failed orders:

```php
<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UsersWithFailedOrdersExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
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
        // Get the count of failed orders for the current user
        $failedOrdersCount = Order::where('user_id', $user->id)
            ->where('status', 'failed')
            ->count();

        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone_number,
            $user->address,
            $user->created_at->format('Y-m-d'),
            $failedOrdersCount, // Append the failed orders count
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
            'Failed Orders Count', // Add a heading for the new column
        ];
    }
}
```

### Step 2: Implement the WithStrictNullComparison Interface
By implementing the WithStrictNullComparison interface, we can ensure that the comparison checks both the value and type, thus displaying 0 instead of an empty string when there are no failed orders.

```php
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

// Add the interface to the class declaration
class UsersWithFailedOrdersExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{
    //...
}
```

### Step 3: Define the Route for Exporting
Add a route in routes/web.php to trigger the export:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-users-with-failed-orders', [ExportsController::class, 'exportUsersWithFailedOrders']);
```

### Step 4: Define the Controller Method
In your ExportsController, define the exportUsersWithFailedOrders method to handle the export logic:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\UsersWithFailedOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportsController extends Controller
{
    public function exportUsersWithFailedOrders()
    {
        return Excel::download(new UsersWithFailedOrdersExport, 'users_with_failed_orders.xlsx');
    }
}
```

### Step 5: Test the Export
Visit the /export-users-with-failed-orders URL in your browser to download the users_with_failed_orders.xlsx file. Verify that the "Failed Orders Count" column displays 0 instead of an empty cell when there are no failed orders for a user.

### Explanation
WithStrictNullComparison: This interface forces Laravel Excel to compare both the value and type of each cell. By implementing this interface, we ensure that 0 is not considered equivalent to an empty string.

```php
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UsersWithFailedOrdersExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{
    // ...
}
```

## Exporting Multiple Sheets in a Single Excel File
Laravel Excel allows you to export multiple sheets within a single Excel file. Let's explore how to create an Excel file with multiple sheets.

### Step-by-Step Guide

### Step 1: Create a Multi-Sheet Export Class
Create a new export class that will handle multiple sheets. You can specify which sheets to include by implementing the WithMultipleSheets interface.

**Create the Export Class**
Run the following command to create a new export class:

```bash
php artisan make:export UsersMultiSheetExport
```
**Modify the Export Class**
Modify the UsersMultiSheetExport class to include multiple sheets:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersMultiSheetExport implements WithMultipleSheets
{
    /**
     * Define the sheets for the export.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new UsersSheetExport(), // First sheet
            new OrdersSheetExport(), // Second sheet
            // Add more sheets as needed
        ];
    }
}
```

### Step 2: Create Individual Sheet Export Classes
Create separate export classes for each sheet you want to include in the Excel file.

**Create Users Sheet Export Class**
```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UsersSheetExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{
    public function collection()
    {
        return User::all();
    }

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

**Create Orders Sheet Export Class**

```php
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersSheetExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Order::all();
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user_id,
            $order->status,
            $order->total_amount,
            $order->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'User ID',
            'Status',
            'Total Amount',
            'Created At',
        ];
    }
}
```

### Step 3: Define the Route for Multi-Sheet Export
Add a route in `routes/web.php` to trigger the multi-sheet export:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-users-multi-sheet', [ExportsController::class, 'exportUsersMultiSheet']);
```

### Step 4: Define the Controller Method for Multi-Sheet Export
In your `ExportsController`, define the `exportUsersMultiSheet` method to handle the export logic:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\UsersMultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportsController extends Controller
{
    public function exportUsersMultiSheet()
    {
        return Excel::download(new UsersMultiSheetExport, 'users_multi_sheet.xlsx');
    }
}
```

### Step 5: Test the Multi-Sheet Export
Visit the `/export-users-multi-sheet` URL in your browser to download the `users_multi_sheet.xlsx` file. Open the file to verify that it contains multiple sheets, each with the specified data.

### Explanation
`WithMultipleSheets:` This interface allows you to define multiple sheets within a single