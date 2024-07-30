# Exporting Reports with Laravel Excel

In this part of the tutorial series, we will learn how to export data from our database into an Excel file using the Laravel Excel package. We will start by creating an export class and setting up a route to download the Excel file.

## Prerequisites

Make sure you have already completed the initial project setup and have data in your database. If not, refer to the previous section of this tutorial to set up the database and seed it with data.

## Step-by-Step Guide to Export Data

### Step 1: Create an Export File

To export data, we first need to create an export file. Laravel Excel provides a command to generate this file. Run the following Artisan command to create a new export class:

```bash
php artisan make:export UsersExport --model=User
```
* **UsersExport:** This is the name of the export file we are creating.

* **--model=User:** This specifies that the export will be based on the User model, which automatically sets up a query to retrieve all user records.

### Step 2: Explore the Export File
After running the command, you will find a new directory named Exports inside the app folder, containing the UsersExport.php file. This file includes a single method named collection that retrieves all records for the specified model (User in this case).

Here's how the UsersExport.php file might look:

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

### Step 3: Create a Route for Exporting Data
Next, we need to create an endpoint to trigger the export and download the Excel file. For simplicity, we'll use a route closure instead of a controller, but you can later move this logic to a dedicated controller if needed.

Add the following route to your routes/web.php file:

```php
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/export-users', function () {
    return Excel::download(new UsersExport, 'users.xlsx');
});
```

* **/export-users:** This is the URL endpoint that triggers the export.
* **Excel::download():** This method takes two parameters:
  * **new UsersExport:** An instance of the export class we created.
  * **'users.xlsx':** The name of the file to be downloaded, with the .xlsx extension.

### Step 4: Download the Excel File
To test the export functionality, visit the /export-users URL in your browser. The browser should automatically download a file named users.xlsx.

### Step 5: Verify the Exported Data
Open the downloaded Excel file to verify that it contains all the user data from the database. You should see all user records listed in the Excel sheet.

## Next Steps
Now that we've successfully exported user data to an Excel file, we can explore additional ways to export data, such as exporting different models, selecting specific fields, and customizing the Excel sheet appearance.

Stay tuned for the next parts of this tutorial series, where we will dive into customizing exports, handling multiple sheets, and applying styles to the Excel files.

## Conclusion
In this section, we learned how to set up a simple export feature using Laravel Excel. We created an export class, defined a route to handle the export request, and downloaded the Excel file containing user data. This serves as a foundational step towards building more advanced reporting features in your Laravel application.

For further details and advanced features, please refer to the Laravel Excel Documentation.

This `README.md` file provides a comprehensive guide for exporting data using the Laravel Excel package, including step-by-step instructions and code examples. You can include this in your project documentation to help others understand and implement the export functionality.