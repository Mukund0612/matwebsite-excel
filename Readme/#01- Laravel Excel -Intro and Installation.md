# #01- Laravel Excel -Intro and Installation

This guide provides instructions on how to use the **Laravel Excel** package to export data to Excel or CSV files and read data from Excel files with multiple sheets. By the end, you'll know how to install the package, configure it, and use it to handle Excel data in your Laravel application.

## Prerequisites

Before getting started, make sure you have:

- Laravel version 5.8 or above
- PHP version 7.2 or above (8.2 is recommended)
- Required PHP extensions enabled in your `php.ini` file

## Installation

### Step 1: Create a New Laravel Project

First, create a new Laravel project and configure your database credentials in the `.env` file.

```bash
composer create-project --prefer-dist laravel/laravel laravel-excel-demo
```
Edit your .env file to include your database details:

```ENV
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
### Step 2: Install Laravel Excel Package
Run the following command to install the Laravel Excel package via Composer:

```bash
composer require maatwebsite/excel
```
If you encounter PHP spreadsheet version errors due to missing dependencies, try running the command with all dependencies:

```bash
composer require maatwebsite/excel --with-all-dependencies
```

### Step 3: Add Service Provider and Alias
The service provider is automatically discovered, but if it doesn't work, you can add it manually.

Open config/app.php and add the service provider to the providers array:

```php
'providers' => [
    // Other service providers...
    Maatwebsite\Excel\ExcelServiceProvider::class,
],
```
Add the alias to the aliases array:

```php
'aliases' => [
    // Other aliases...
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
],
```

### Step 4: Publish the Configuration
Run the following command to publish the package configuration file:

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

This will create a file named excel.php in the config folder of your project. You can adjust the configuration settings according to your needs.

Exporting Data to Excel
Let's start by exporting data to an Excel file.

### Step 1: Create a New Export Class
Run the Artisan command to create a new export class:

```bash
php artisan make:export UsersExport --model=User
```
This command will create a new class UsersExport in the App/Exports directory.

### Step 2: Implement the Export Class
Open the UsersExport.php file and implement the collection method:

```php
<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all();
    }
}
```

### Step 3: Create a Route and Controller Method
Add a new route in routes/web.php to trigger the export:

```php
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/export-users', function () {
    return Excel::download(new UsersExport, 'users.xlsx');
});
```

### Step 4: Test the Export
Visit the /export-users URL in your browser. If everything is set up correctly, the browser should download a file named users.xlsx containing all the user data.

Importing Data from Excel
Next, let's import data from an Excel file with multiple sheets.

### Step 1: Create a New Import Class
Run the Artisan command to create a new import class:

```bash
php artisan make:import UsersImport --model=User
```

### Step 2: Implement the Import Class
Open the UsersImport.php file and implement the model method:

```php
<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => bcrypt($row['password']),
        ]);
    }
}
```

### Step 3: Create a Route and Controller Method for Import
Add a new route in routes/web.php to trigger the import:

```php
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

Route::post('/import-users', function (Request $request) {
    Excel::import(new UsersImport, $request->file('file'));

    return redirect('/')->with('success', 'Users imported successfully!');
});
```

### Step 4: Create a Form to Upload Excel File
Create a simple form in a Blade view to upload the Excel file:

```html
<!-- resources/views/import.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Users</title>
</head>
<body>
    <h1>Import Users</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="/import-users" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Users</button>
    </form>
</body>
</html>
```

### Step 5: Test the Import
Create a view route in routes/web.php to display the form:

```php
Route::get('/import-form', function () {
    return view('import');
});
```

Visit the /import-form URL in your browser, upload an Excel file, and submit the form. The users from the Excel file should be imported into the database.

Conclusion
You've successfully learned how to use Laravel Excel to export and import data with Laravel. You can explore further by customizing the export and import logic to fit your specific requirements.

For more detailed documentation and advanced usage, refer to the Laravel Excel Documentation.

arduino
Copy code

This `README.md` file provides a clear and structured guide for using the Laravel Excel package, in