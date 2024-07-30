# Advanced Data Export with Laravel Excel

In this section of the tutorial, we'll explore different ways to export data using the Laravel Excel package. We'll cover exporting data from collections, arrays, queries, and Blade views, and learn how to include headings in your Excel files.

## Prerequisites

Before proceeding, ensure you have completed the initial setup and have a working Laravel project with data in your database.

## Exporting Data: Different Approaches

### 1. Exporting with Collections

We've already seen how to export data using collections. This method is suitable when you want to export data exactly as it is in the database. 

### 2. Exporting with Arrays

Sometimes, you might want to manipulate or prepare data before exporting it. In such cases, exporting data as an array is a great option. Here's how you can achieve that:

#### Step-by-Step Instructions

1. **Create an Export File**: We'll use a controller to prepare data and pass it to the export file as a constructor parameter.

2. **Create a Controller**:
  Run the following command to create a controller named `ExportsController`:

```bash
php artisan make:controller ExportsController
```

3. Define the Export Method:

In the ExportsController, define a method called export to retrieve and prepare data:

```php
<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\User;

class ExportsController extends Controller
{
    public function export()
    {
        $data = User::all()->toArray(); // Prepare data as an array

        return Excel::download(new ArrayExport($data), 'users_array.xlsx');
    }
}
```
4. Create the Export Class:

Run the following command to create an export class named ArrayExport:

```bash
php artisan make:export ArrayExport
```
5. Implement the FromArray Interface:

Modify the ArrayExport class to implement the FromArray interface:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ArrayExport implements FromArray
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
}
```
6. Create a Route:

Add the following route in routes/web.php:

```php
use App\Http\Controllers\ExportsController;

Route::get('/export-array', [ExportsController::class, 'export']);
```

7. Test the Export:

Visit the /export-array URL in your browser to download the users_array.xlsx file.

### 3. Exporting with Queries
Another way to export data is by using queries. This method is efficient for handling large datasets as it processes data in chunks.

**Step-by-Step Instructions**
1. Create an Export Class:**

Run the following command to create an export class:

```bash
php artisan make:export QueryExport --model=Order
```
2. Implement the FromQuery Interface:

Modify the QueryExport class to implement the FromQuery interface:

```php
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;

class QueryExport implements FromQuery
{
    public function query()
    {
        return Order::query()->where('total_price', '>', 50);
    }
}
```
3. Create a Route:

Add the following route in routes/web.php:

```php
use App\Exports\QueryExport;

Route::get('/export-query', function () {
    return Excel::download(new QueryExport, 'orders_above_50.xlsx');
});
```

4. Test the Export:

Visit the **/export-query** URL in your browser to download the orders_above_50.xlsx file.

### 4. Exporting with Blade Views
Exporting data using Blade views allows you to customize the Excel file with HTML and include additional elements like headings.

**Step-by-Step Instructions**
1. Create a Blade View:

Create a Blade file named orders.blade.php inside the resources/views folder:

```html
<!-- resources/views/orders.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Report</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user_id }}</td>
                <td>{{ $order->total_price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
```
2. Create an Export Class:

Run the following command to create an export class named OrdersViewExport:

```bash
php artisan make:export OrdersViewExport
```
3. Implement the FromView Interface:

Modify the OrdersViewExport class to implement the FromView interface:

```php
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class OrdersViewExport implements FromView
{
    public function view(): View
    {
        return view('orders', [
            'orders' => Order::all()
        ]);
    }
}
```
4. Create a Route:

Add the following route in routes/web.php:

```php
use App\Exports\OrdersViewExport;

Route::get('/export-view', function () {
    return Excel::download(new OrdersViewExport, 'orders_view.xlsx');
});
```

5. Test the Export:

Visit the /export-view URL in your browser to download the orders_view.xlsx file. You will notice the Excel file now includes the table structure defined in the Blade view.

### Adding Headings to Exports
By default, when exporting data as collections or arrays, the Excel file does not include column headings. Here's how to add headings to your exports:

**Implement the WithHeadings Interface**
1. Modify the Export Class:

Add the WithHeadings interface to your export class and define the headings method:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings
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
2. Test the Export:

Re-run the export route (/export-array), and you'll see the Excel file now includes the specified headings.

## Conclusion
This section has demonstrated various ways to export data using the Laravel Excel package. By utilizing collections, arrays, queries, and Blade views, you can customize your exports to suit your application's needs. Additionally, adding headings to your exports enhances the readability of your Excel files.

Explore these methods to create robust reporting features in your Laravel application. For more advanced options and customizations, refer to the Laravel Excel Documentation.

This `README.md` file covers the steps for exporting data using various methods and includes detailed explanations and code examples for each approach. You can use this guide to enhance the data export functionality in your Laravel application.