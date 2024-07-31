# Laravel Excel: Customizing Exports with Events

Laravel Excel allows you to leverage various events during the export process to add custom behavior and enhance your exports. This guide will walk you through the different events available and how to use them effectively.

## Table of Contents

1. [Introduction](#introduction)
2. [Events Overview](#events-overview)
3. [Using Events in Exports](#using-events-in-exports)
   - [Method 1: WithEvents Interface](#method-1-withevents-interface)
   - [Method 2: RegisterEvents Trait](#method-2-registerevents-trait)
4. [Common Use Cases for Events](#common-use-cases-for-events)
5. [Handling Right-to-Left (RTL) Sheets](#handling-right-to-left-rtl-sheets)
6. [Conclusion](#conclusion)

## Introduction

Laravel Excel provides a set of events that can be used to customize and extend the export functionality. These events allow you to add custom logic at various stages of the export process, such as styling sheets, setting properties, and more.

## Events Overview

The following events are available in Laravel Excel:

- **BeforeExport**: Triggered before the export process starts.
- **BeforeWriting**: Triggered when the export is completed and about to be stored or downloaded.
- **BeforeSheet**: Triggered when a sheet is created but before any data is written to it.
- **AfterSheet**: Triggered after the sheet creation process is completed. This is the most commonly used event for custom logic.

## Using Events in Exports

There are two primary ways to use events in Laravel Excel:

### Method 1: WithEvents Interface

In this method, you implement the `WithEvents` interface and define a `registerEvents` method in your export class. This method returns an array of events and their corresponding event listeners.

#### Example: Using the WithEvents Interface

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\User;

class UserExport implements FromQuery, WithMapping, WithHeadings, ShouldQueue, WithEvents
{
    use Exportable;

    /**
     * Return the query for the export.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return User::query();
    }

    /**
     * Map the data for each row.
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
        ];
    }

    /**
     * Define the headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['ID', 'Name', 'Email'];
    }

    /**
     * Register events for the export.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event) {
                // Logic before export starts
                info('Export is about to start.');
            },
            BeforeWriting::class => function(BeforeWriting $event) {
                // Logic before writing to disk
                info('Export is about to be written.');
            },
            BeforeSheet::class => function(BeforeSheet $event) {
                // Logic before a sheet is created
                info('Sheet is about to be created.');
            },
            AfterSheet::class => function(AfterSheet $event) {
                // Logic after a sheet is created
                $sheet = $event->sheet;
                $sheet->getDelegate()->getStyle('A1:C1')->getFont()->setBold(true);
                info('Sheet creation completed.');
            },
        ];
    }
}
```

**Note on Closures**
While using closures is convenient, they cannot be used for queued exports because PHP cannot serialize closures. If you need to queue exports, consider using invocable class instances or the `RegisterEvents` trait.

### Method 2: RegisterEvents Trait
The second method involves using the `RegisterEvents` trait along with the `WithEvents` interface. This approach allows you to define each event listener as a static method on the export class.

**Example: Using the RegisterEvents Trait**
```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\User;

class UserExport implements FromQuery, WithMapping, WithHeadings, ShouldQueue, WithEvents
{
    use Exportable, RegistersEventListeners;

    // Other methods and interfaces

    /**
     * Logic before export starts.
     *
     * @param BeforeExport $event
     */
    public static function beforeExport(BeforeExport $event)
    {
        info('Export is about to start.');
    }

    /**
     * Logic before writing to disk.
     *
     * @param BeforeWriting $event
     */
    public static function beforeWriting(BeforeWriting $event)
    {
        info('Export is about to be written.');
    }

    /**
     * Logic before a sheet is created.
     *
     * @param BeforeSheet $event
     */
    public static function beforeSheet(BeforeSheet $event)
    {
        info('Sheet is about to be created.');
    }

    /**
     * Logic after a sheet is created.
     *
     * @param AfterSheet $event
     */
    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet;
        $sheet->getDelegate()->getStyle('A1:C1')->getFont()->setBold(true);
        info('Sheet creation completed.');
    }
}
```

### Global Event Listeners
If you have multiple export files and want to define a generic listener for all of them, you can do so inside the `AppServiceProvider` file in the `register` method.

**Example: Defining Global Event Listeners**
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Excel::registerListeners([
            AfterSheet::class => function(AfterSheet $event) {
                // Global logic for after sheet
                $event->sheet->getDelegate()->getStyle('A1:C1')->getFont()->setBold(true);
            },
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```

## Common Use Cases for Events
Events in Laravel Excel can be used for a variety of tasks, including:

- **Adding Properties:** You can set properties like author name, title, or description for the Excel file.
- **Setting Orientation:** Change the sheet's orientation to landscape or portrait.
- **Styling Sheets:** Apply styles such as font size, color, and alignment to cells or ranges.
- **Data Manipulation:** Modify data or add additional data to the sheet before or after writing.

## Handling Right-to-Left (RTL) Sheets
For languages that require Right-to-Left (RTL) formatting, you can easily enable RTL support in the `afterSheet` method.

### Example: Enabling RTL Support
```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\User;

class UserExport implements FromQuery, WithMapping, WithHeadings, ShouldQueue, WithEvents
{
    use Exportable, RegistersEventListeners;

    // Other methods and interfaces

    /**
     * Logic after a sheet is created.
     *
     * @param AfterSheet $event
     */
    public static function afterSheet(AfterSheet $event)
    {
        // Enable RTL (Right-to-Left) support
        $sheet = $event->sheet->getDelegate();
        $sheet->setRightToLeft(true);
    }
}
```

In the above code, the `setRightToLeft(true)` method is used to enable RTL formatting for the sheet.

### Conclusion
Events in Laravel Excel provide a powerful way to customize and extend the export process. By leveraging these events, you can add sophisticated features and improve the overall functionality of your exports.

Feel free to experiment with different events and custom logic to suit your specific requirements. For more information, check out the [Laravel Excel documentation](https://laravel-excel.com/ "Laravel Excel documentation").

***

Thank you for reading this guide! If you found it helpful, please consider giving a thumbs up and subscribing for more content.

This `README.md` file includes clear explanations of how to use events in Laravel Excel, complete with example code and guidance on common use cases. Feel free to modify it further to suit your needs!