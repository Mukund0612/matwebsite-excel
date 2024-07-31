# Laravel Excel: Customizing Export Columns and Styles

In this guide, we will explore how to customize Excel exports using Laravel Excel. This includes adjusting column widths, applying styles to headers and entire sheets, and implementing text wrapping for cells.

## Table of Contents

1. [Current Export Overview](#current-export-overview)
2. [Auto-Resizing Columns](#auto-resizing-columns)
3. [Manually Setting Column Widths](#manually-setting-column-widths)
4. [Styling the Header Row](#styling-the-header-row)
5. [Applying Default Styles](#applying-default-styles)
6. [Text Wrapping for Cells](#text-wrapping-for-cells)

## Current Export Overview

Before making any changes, let's review what the current export looks like. In many cases, columns may overlap, or the text might not display properly due to sizing issues. This can make the spreadsheet difficult to read and aesthetically unappealing.

## Auto-Resizing Columns

To automatically resize columns based on their content, we can implement the `ShouldAutoSize` interface. This will make sure that each column adjusts its width to fit the content inside it.

### Implementing Auto-Resizing

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromQuery, WithMapping, ShouldAutoSize
{
    // Implement your query and mapping methods

    // No additional methods required for ShouldAutoSize
}
```

### Result
After implementing `ShouldAutoSize`, the columns will automatically adjust to fit their content, providing a cleaner, more organized appearance.

## Manually Setting Column Widths
Sometimes you may want to set specific widths for certain columns manually. This can be useful for columns that require more or less space than what auto-sizing provides.

### Removing Auto-Sizing
First, remove the ShouldAutoSize interface:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromQuery, WithMapping, WithColumnWidths
{
    // Implement your query and mapping methods

    /**
     * Define column widths.
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID column
            'B' => 55,  // Notes column
            'C' => 20,  // Status column
            'D' => 25,  // Amount column
            'E' => 30,  // Another column
        ];
    }
}
```

## Combining Manual and Auto-Sizing
You can combine manual widths with auto-sizing to only set widths for specific columns while allowing others to adjust automatically:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromQuery, WithMapping, WithColumnWidths, ShouldAutoSize
{
    // Implement your query and mapping methods

    /**
     * Define column widths.
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'B' => 55,  // Specific column width for Notes
        ];
    }
}
```

### Result
With this approach, specific columns will have the width you define, while others will adjust automatically, providing a more flexible layout.

## Styling the Header Row
Styling the header row can significantly improve the readability of your Excel sheets. You can make the header bold or apply other styles to distinguish it from the data rows.

### Implementing Header Styles
To style the header row, implement the WithStyles interface:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements WithStyles
{
    // Other interfaces and methods

    /**
     * Define styles for the sheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],  // Make the first row (header) bold
        ];
    }
}
```

### PHP Syntax for Styling
You can also use a PHP-style approach to apply styles:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements WithStyles
{
    // Other interfaces and methods

    /**
     * Define styles using PHP syntax.
     *
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);  // Bold font for the first row
    }
}
```

### Result
The header row will now have a bold font, making it easier to differentiate from the data rows.

## Applying Default Styles
If you want to apply consistent styling across the entire sheet, you can use the `WithDefaultStyles` interface. Note that this is only available from version 3.4.1 onwards.

### Implementing Default Styles
```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDefaultStyles;

class OrderExport implements WithDefaultStyles
{
    // Other interfaces and methods

    /**
     * Apply default styles to the sheet.
     *
     * @return array
     */
    public function defaultStyles(): array
    {
        return [
            'font' => [
                'name' => 'Calibri',
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ];
    }
}
```
### Result
All cells in the sheet will now have the Calibri font set to size 12, with both horizontal and vertical text alignment set to center.

### Note
The `WithDefaultStyles` interface is only available in Laravel Excel version 3.4.1 or newer.

## Text Wrapping for Cells
Text wrapping is useful for columns that contain longer text, ensuring that content is displayed within the cell without being cut off.

### Implementing Text Wrapping
To enable text wrapping, you can modify the styles for specific columns:

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements WithStyles
{
    // Other interfaces and methods

    /**
     * Define styles for wrapping text.
     *
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow(); // Get the highest row number

        // Wrap text in column B (Notes column) from row 1 to the highest row
        $sheet->getStyle("B1:B{$highestRow}")->getAlignment()->setWrapText(true);
    }
}
```

### Result
The content in the specified column will now wrap within the cell, providing better readability for long text entries.

## Conclusion
In this guide, we covered various techniques to customize Excel exports using Laravel Excel:

* Auto-resizing columns to fit content
* Manually setting column widths
* Styling headers and applying default styles
* Enabling text wrapping for specific columns

These customization options allow you to create more readable and visually appealing Excel reports tailored to your specific requirements.

Thank you for following along! With these techniques, you can now create Excel files with multiple sheets and customize columns and styles as needed.

This `README.md` provides a comprehensive overview of how to customize your Excel exports with Laravel Excel, including practical examples and code snippets for each feature discussed.