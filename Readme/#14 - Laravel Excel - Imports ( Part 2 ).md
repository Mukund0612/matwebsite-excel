# Importing Excel Files with Laravel: Advanced Techniques

Welcome to the second part of our series on importing Excel files with Laravel. In this guide, we will explore advanced techniques for handling multiple sheets and optimizing import performance.

## Overview

In the previous video, we covered basic import functionalities. If you haven't watched that yet, please check it out first. [Previous Video](#)

In this video, we will:

1. **Handle Multiple Sheets**
2. **Optimize Import Performance**
3. **Display Progress in Terminal**

## Handling Multiple Sheets

### Setup

We will work with an Excel file that contains multiple sheets, each representing orders for a specific user. The file has two sheets with the following structure:

- **Sheet 1**: Orders for User A
- **Sheet 2**: Orders for User B

### Step-by-Step Guide

1. **Create Import Classes**

    Run the following commands to create import classes for users and orders:

    ```bash
    php artisan make:import UserOrdersImport --model=User
    php artisan make:import OrdersImport --model=Order
    ```
2. **Define the Sheets Concern**

    Update the UserOrdersImport file to handle multiple sheets:

    ```php
    namespace App\Imports;

    use Maatwebsite\Excel\Concerns\Importable;
    use Maatwebsite\Excel\Concerns\WithMultipleSheets;

    class UserOrdersImport implements WithMultipleSheets
    {
        use Importable;

        public function sheets(): array
        {
            return [
                'orders' => new OrdersImport(),
            ];
        }
    }
    ```
3. Implement the OrdersImport Class

    Update the `OrdersImport` class to handle data insertion:

    ```php
    namespace App\Imports;

    use App\Models\Order;
    use App\Models\User;
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithBatchInserts;
    use Maatwebsite\Excel\Concerns\WithChunkReading;
    use Maatwebsite\Excel\Concerns\WithProgressBar;

    class OrdersImport implements ToModel, WithBatchInserts, WithChunkReading, WithProgressBar
    {
        public function model(array $row)
        {
            $user = User::where('name', $row[0])->first();
            if ($user) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'item' => $row[1],
                    'quantity' => $row[2],
                    'price' => $row[3],
                ]);
                // Add order items if necessary
            }

            return $order;
        }

        public function batchSize(): int
        {
            return 5000; // Define batch size for insertion
        }

        public function chunkSize(): int
        {
            return 1000; // Define chunk size for reading
        }
    }
    ```

4. **Update the Controller**

    In your controller, use the UserOrdersImport class:

    ```php
    namespace App\Http\Controllers;

    use App\Imports\UserOrdersImport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Http\Request;

    class ImportController extends Controller
    {
        public function import(Request $request)
        {
            $file = $request->file('excel_file');
            Excel::import(new UserOrdersImport, $file);

            return redirect()->back()->with('success', 'Data imported successfully.');
        }
    }
    ```

## Optimizing Import Performance
### Batch Inserts
To reduce the number of queries executed, implement batch inserts:

```php
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class OrdersImport implements ToModel, WithBatchInserts
{
    // ...
    public function batchSize(): int
    {
        return 5000; // Define batch size
    }
}
```

### Chunk Reading
To handle large files and manage memory usage, implement chunk reading:

```php
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OrdersImport implements ToModel, WithChunkReading
{
    // ...
    public function chunkSize(): int
    {
        return 1000; // Define chunk size
    }
}
```

## Displaying Progress in Terminal
To show progress in the terminal, use the `WithProgressBar` concern:

```php
use Maatwebsite\Excel\Concerns\WithProgressBar;

class OrdersImport implements ToModel, WithProgressBar
{
    // ...
}
```
When running the import via command line, the progress bar will display.

## Conclusion
In this guide, we've covered advanced techniques for importing Excel files with Laravel, including handling multiple sheets, optimizing performance, and displaying progress. We hope you find this information helpful!

If you found this guide useful, please like and share it with others.

Thank you for watching, and see you in the next one!

This `README.md` provides a structured overview of the advanced import techniques, including code examples and explanations.