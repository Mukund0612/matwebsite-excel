# Laravel Excel: Exporting Multiple Sheets

In this guide, we will learn how to export an Excel file with multiple sheets using Laravel Excel. Each sheet will contain order history for a specific user. We will create a main export file that includes a separate sheet for each user, each populated with their respective order data.

## Step-by-Step Guide

### Step 1: Create a New Export File for User Sheets

First, we need to create a new export file that will represent the individual sheets for each user.

Run the following Artisan command to create the export class:

```bash
php artisan make:export UserSheetExport
```

### Step 2: Modify the Main Export File
Let's update the main export file to implement multiple sheets.

Update the Main Export Class
In this example, we will use a main export class called `UsersMultiSheetExport`. This class will implement the `WithMultipleSheets` interface to manage multiple sheets.

```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersMultiSheetExport implements WithMultipleSheets
{
    protected $users;

    /**
     * Constructor to initialize user data.
     *
     * @param \Illuminate\Support\Collection $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Define the sheets for the export.
     *
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Loop through each user and create a sheet for each
        foreach ($this->users as $user) {
            $sheets[] = new UserSheetExport($user);
        }

        return $sheets;
    }
}
```

`WithMultipleSheets:` This interface allows us to specify multiple sheets in a single Excel file. We loop through each user and create a new `UserSheetExport` for each user, adding it to the sheets array.

### Step 3: Define the Individual User Sheet Export Class
Now let's modify the `UserSheetExport` class to define each sheet's content and title.

**Update the User Sheet Export Class**
```php
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSheetExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $user;

    /**
     * Constructor to initialize the user.
     *
     * @param \App\Models\User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Define the query for the orders of the specific user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        // Retrieve orders for the specific user
        return Order::query()->where('user_id', $this->user->id);
    }

    /**
     * Define the headings for the sheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Status',
            'Amount',
            'Notes',
        ];
    }

    /**
     * Map each row of the sheet.
     *
     * @param mixed $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->id,
            $order->status,
            $order->amount,
            $order->notes,
        ];
    }

    /**
     * Define the title of the sheet.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->user->name;
    }
}
```

* `WithTitle:` This interface allows us to set the title of each sheet. We use the user's name as the title for their respective sheet.

* `FromQuery:` This interface lets us define a query to retrieve data for the sheet. We use this to get all orders for the specific user.

* `WithHeadings:` This interface allows us to define custom headings for each sheet.

* `WithMapping:` This interface lets us map and customize the data that will appear in each row of the sheet.

### Step 4: Define the Route for Exporting
Add a route in routes/web.php to handle the export request:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-users-multiple-sheets', [ExportsController::class, 'exportUsersMultipleSheets']);
```

### Step 5: Define the Controller Method
In your ExportsController, define the exportUsersMultipleSheets method to handle the export logic:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\UsersMultiSheetExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportsController extends Controller
{
    public function exportUsersMultipleSheets()
    {
        // Retrieve all users
        $users = User::all();

        // Download the Excel file
        return Excel::download(new UsersMultiSheetExport($users), 'users_multiple_sheets.xlsx');
    }
}
```

* `Excel::download():` This method generates the Excel file and prompts the user to download it.

### Step 6: Test the Export
Visit the `/export-users-multiple-sheets` URL in your browser to download the `users_multiple_sheets.xlsx` file. Open the file to verify that it contains a separate sheet for each user, each displaying their order history.

## Explanation
* `Multiple Sheets:` Each sheet in the exported file represents a single user and contains the user's order history.

* `Dynamic Sheet Titles:` The title of each sheet is dynamically set to the user's name.

* `Custom Mappings and Headings:` We define custom headings and map the data to control how it appears in the Excel file.

## Conclusion
You have now learned how to create an Excel export with multiple sheets using Laravel Excel. This approach allows for greater organization and customization of your Excel reports, making it easy to present data in a structured and user-friendly manner.

In the next section, we will explore further customization options for columns and cells within each sheet.

This `README.md` provides a clear and detailed explanation of how to set up an export with multiple sheets in Laravel Excel, covering the creation of classes, routes, and controller methods required for the export.