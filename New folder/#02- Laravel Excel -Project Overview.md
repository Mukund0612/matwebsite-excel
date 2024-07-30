# Laravel Excel Project: Restaurant Reporting System

This project demonstrates how to use the **Laravel Excel** package to generate reports for a restaurant management system. Throughout this tutorial series, we'll be working with a database structure designed to store information about restaurants, their menu items, and customer orders.

## Project Overview

The main objective of this project is to generate different types of reports using data from a simplified restaurant management system. The project scope is small enough to keep things manageable but comprehensive enough to cover all necessary topics.

### Database Structure

The database is designed to store the following information:

1. **Restaurants**: Information about each restaurant, such as name, email, and address.
2. **Menu Items**: Details about the items offered by each restaurant.
3. **Orders**: Information about orders placed by users.
4. **Order Items**: Details of each item included in an order and the total price.

### Entity-Relationship Diagram (ERD)

Here's a simplified ERD representing the database structure:

```plaintext
+----------------+        +---------------+        +--------------+
|  Restaurants   |        |  Menu Items   |        |   Orders     |
|----------------|        |---------------|        |--------------|
| id             | 1    n | id            |        | id           |
| name           |<------>| restaurant_id |        | user_id      |
| email          |        | name          |        | total_price  |
| address        |        | price         |        | created_at   |
+----------------+        +---------------+        +--------------+
                                 | 1
                                 |
                                 | n
                          +--------------+
                          | Order Items  |
                          |--------------|
                          | id           |
                          | order_id     |
                          | menu_item_id |
                          +--------------+
```

## Laravel Migrations
To set up the database schema, we use Laravel migrations. Here's a brief overview of each migration:

1. **Restaurants Migration:** Contains columns for storing the name, email, and address of each restaurant.

```php
Schema::create('restaurants', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('address');
    $table->timestamps();
});
```

2. **Menu Items Migration:** Represents the items offered by a restaurant.

```php
Schema::create('menu_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->decimal('price', 8, 2);
    $table->timestamps();
});
```
3. **Orders Migration:** Holds information about orders placed by users.

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('total_price', 8, 2);
    $table->timestamps();
});
```

3. **Order Items Migration:** Stores details of each item included in an order.

```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

## Laravel Models and Relationships
In the models, we define fillable attributes and relationships between tables. Here are some examples:

**Restaurant Model**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['name', 'email', 'address'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
```
**MenuItem Model**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['name', 'price'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
```

**Order Model**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['total_price'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

**OrderItem Model**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
```

## Database Seeders and Factories
To populate the database with fake data, we use Laravel seeders and factories.

### Factories
In the factories, we use the PHP Faker package to generate fake data for each table:

**Restaurant Factory**
```php
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->companyEmail,
            'address' => $this->faker->address,
        ];
    }
}
```

**MenuItem Factory**
```php
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
```

**Order Factory**
```php
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 20, 500),
        ];
    }
}
```

**OrderItem Factory**
```php
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'menu_item_id' => MenuItem::factory(),
        ];
    }
}
```

**Seeder**
In the database seeder file, we call these factories to insert the data into the database:

```php
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Restaurant::factory(10)->hasMenuItems(5)->create();
        Order::factory(50)->has(OrderItem::factory()->count(3))->create();
    }
}
```

**Setup Instructions**
1. Clone the repository from GitHub.
2. Set the database credentials in your .env file.
3. Run the migrations and seed the database with the following command:

```bash
php artisan migrate --seed
```
This command will create all the tables and insert fake data into the database.

## Next Steps
Once your database is set up, you can start generating reports by exporting data using Laravel Excel.

For detailed instructions on exporting data, see the Laravel Excel Export Guide.

## Conclusion
This setup provides a foundational structure for a restaurant reporting system. You can customize and extend it to fit your specific needs as you continue to explore the features of the Laravel Excel package.

For more information and advanced usage, refer to the Laravel Excel Documentation.

arduino

This `README.md` file gives a comprehensive overview of the database setup and initial configuration for the project. It provides instructions on setting up the database and preparing for data exports using Laravel Excel.