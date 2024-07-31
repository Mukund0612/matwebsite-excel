# Laravel Excel: Importing Excel Files

In this guide, we'll explore how to import Excel files into a Laravel application using Laravel Excel. We'll cover importing simple and complex Excel files, handling multiple sheets, and leveraging various interfaces and features to make the import process efficient and flexible.

## Table of Contents

1. [Introduction](#introduction)
2. [Creating an Import Class](#creating-an-import-class)
3. [Basic Import with ToModel Interface](#basic-import-with-tomodel-interface)
4. [Handling Duplicates with Observers](#handling-duplicates-with-observers)
5. [Skipping Rows Conditionally](#skipping-rows-conditionally)
6. [Using the Importable Trait](#using-the-importable-trait)
7. [Working with Heading Rows](#working-with-heading-rows)
8. [Conclusion](#conclusion)

## Introduction

Laravel Excel simplifies the process of importing Excel and CSV files into your Laravel application. It provides an elegant and intuitive API to handle both simple and complex import scenarios, including multiple sheets, custom headers, and more.

## Creating an Import Class

To get started with importing Excel files, we need to create an import class. This class will define how we handle the incoming Excel data.

### Step 1: Create an Import Class

Run the following Artisan command to create a new import class:

```bash
php artisan make:import UserImport
```
By default, this creates a new import class in the `App\Imports` directory.

### Step 2: Implement the ToModel Interface
The import class by default implements the ToCollection interface, but for most use cases, you might want to switch to the ToModel interface. This interface allows you to map each row of the Excel file to a model.

**Example: UserImport Class with ToModel Interface**
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    /**
     * Map each row to a User model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'first_name' => $row[0],
            'email' => $row[1],
            'phone_number' => $row[2],
            'password' => Hash::make($row[3]),
            'address' => $row[4],
        ]);
    }
}
```
In this example, each row from the Excel file is mapped to a new User model instance.

## Basic Import with ToModel Interface
After creating the import class, you can use it to import data from an Excel file.

### Step 1: Create a Route and Controller Method
Define a route and a corresponding controller method to trigger the import.

```php
// routes/web.php

use App\Http\Controllers\UserController;

Route::get('/import', [UserController::class, 'import']);
```

### Step 2: Implement the Import Logic
In your controller, use the Excel facade to import the file.

```php
<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Import Excel file data into the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        Excel::import(new UserImport, 'users.xlsx', 'local');

        return response()->json(['success' => true]);
    }
}
```

### Explanation
- **First Parameter:** The import class instance (`new UserImport`).
- **Second Parameter:** The filename of the Excel file to import (`'users.xlsx'`).
- **Third Parameter:** (Optional) The disk where the file is stored. Defaults to `'local'`.

### Running the Import
1. Move your Excel file to the storage app folder.
2. Access the import route in your browser: http://your-app-url/import.

Check your database to verify that the data has been imported successfully.

## Handling Duplicates with Observers
When importing data, you might encounter duplicate entries, especially if you have unique columns like email. Laravel Excel provides a way to handle this using the WithUpserts interface.

### Step 1: Implement the WithUpserts Interface
Add the `WithUpserts` interface to your import class and define the `uniqueBy` method.

**Example: Handling Duplicates**
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class UserImport implements ToModel, WithUpserts
{
    /**
     * Map each row to a User model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'first_name' => $row[0],
            'email' => $row[1],
            'phone_number' => $row[2],
            'password' => Hash::make($row[3]),
            'address' => $row[4],
        ]);
    }

    /**
     * Specify the unique column for upserts.
     *
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'email';
    }
}
```

### Step 2: Define the Unique Column
In the `uniqueBy` method, return the column name that should be unique (e.g., `email`). This will ensure that if a duplicate is found, the existing entry will be updated instead of creating a new one.

### Updating Specific Columns
You can specify which columns to update in case of duplication by implementing the `WithUpsertColumns` interface and defining the `upsertColumns` method.

**Example: Specifying Columns to Update**
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;

class UserImport implements ToModel, WithUpserts, WithUpsertColumns
{
    // Other methods

    /**
     * Specify which columns to update on duplicate.
     *
     * @return array
     */
    public function upsertColumns()
    {
        return ['first_name', 'phone_number', 'address'];
    }
}
```

## Skipping Rows Conditionally
You may want to skip certain rows based on specific conditions. This can be done by returning `null` in the `model` method for rows that you want to skip.

### Example: Skipping Rows Without a Name
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    // Other methods

    public function model(array $row)
    {
        if (empty($row[0])) {
            return null; // Skip rows without a name
        }

        return new User([
            'first_name' => $row[0],
            'email' => $row[1],
            'phone_number' => $row[2],
            'password' => Hash::make($row[3]),
            'address' => $row[4],
        ]);
    }
}
```

## Using the Importable Trait
Just like the `Exportable` trait is used in export classes, the `Importable` trait can be used in import classes for a more fluent API.

### Example: Using the Importable Trait
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class UserImport implements ToModel
{
    use Importable;

    // Other methods
}
```

### Using the Importable Trait in Controller
```php
<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;

class UserController extends Controller
{
    /**
     * Import Excel file data using Importable trait.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        (new UserImport)->import('users.xlsx', 'local');

        return response()->json(['success' => true]);
    }
}
```

This approach allows you to call the `import` method directly on the import class instance, providing a more cohesive syntax.

## Working with Heading Rows
In many cases, your Excel files may include a heading row that contains column names. Laravel Excel provides the `WithHeadingRow` interface to handle this scenario.

### Example: Using the `WithHeadingRow` Interface
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row to a User model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'first_name' => $row['first_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'],
            'password' => Hash::make($row['password']),
            'address' => $row['address'],
        ]);
    }
}
```

### Specifying a Custom Heading Row
If your heading row is not at the top, you can specify its position by defining the headingRow method.

**Example: Specifying a Custom Heading Row**
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    // Other methods

    /**
     * Specify the heading row position.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 3; // Heading row is at row 3
    }
}
```

### Handling Heading Formatting
By default, Laravel Excel formats heading keys using the `Str::slug` helper, converting them to lowercase and replacing spaces with underscores. You can customize this behavior by defining your own formatting logic in a service provider using the extend method.

### Example: Customizing Heading Formatting
```php
<?php

namespace App\Providers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Excel::extend('customHeadingFormatter', function ($value) {
            return 'custom_' . strtolower(str_replace(' ', '_', $value));
        });
    }
}
```

## Conclusion
Importing Excel files into Laravel using Laravel Excel is a powerful and flexible process. With interfaces like `ToModel`, `WithUpserts`, `WithHeadingRow`, and traits like `Importable`, you can handle various import scenarios efficiently.

This guide covered the basics of importing, handling duplicates, skipping rows, and using heading rows. Experiment with these features to suit your specific import needs.

Stay tuned for the next part of this series, where we'll explore importing multiple sheets, batch inserts, chunks, and more advanced techniques.

Thank you for reading this guide! If you found it helpful, please consider giving a thumbs up and subscribing for more content.

This `README.md` file provides a structured overview of importing Excel files using Laravel Excel, including code examples and explanations. Feel free to customize it further to match your project's needs!