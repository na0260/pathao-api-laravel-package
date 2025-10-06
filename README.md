
# Pathao Laravel API Package

This package provides a complete Laravel integration with the Pathao Courier Merchant API. You can manage stores, orders, bulk orders, price calculation, and fetch cities, zones, and areas.

## Installation

Install via Composer:

```bash
composer require na/pathao-laravel-api-package:@dev
```

## Configuration

Publish the config file (optional):

```bash
php artisan vendor:publish --provider="Nur\Pathao\PathaoServiceProvider" --tag="config"
```

Add credentials in `.env`:

```
PATHAO_CLIENT_ID=your_client_id
PATHAO_CLIENT_SECRET=your_client_secret
PATHAO_USERNAME=your_email
PATHAO_PASSWORD=your_password
PATHAO_SANDBOX=false (if you want to use sandbox use true, otherwise false)
```

## Usage

All calls can be made via the `Pathao` facade.

### Cities, Zones, Areas

```php
$cities = Pathao::cities();
$zones = Pathao::zones($cityId);
$areas = Pathao::areas($zoneId);
```

### Stores

#### Create Store
```php
$store = Pathao::createStore([
    'name' => 'Test Store',
    'contact_name' => 'John Doe',
    'contact_number' => '017XXXXXXXX',
    'address' => 'House 123, Road 4, Dhaka',
    'city_id' => {{city_id}},
    'zone_id' => {{zone_id}},
    'area_id' => {{area_id}},
]);
```
#### Store Info
```php
$stores = Pathao::storeInfo();
```

### Orders

#### Create Order

```php
$order = Pathao::createOrder([
    'store_id' => {{merchant_store_id}},
    'merchant_order_id' => "{{merchant_order_id}}",
    'recipient_name' => 'John Doe',
    'recipient_phone' => '017XXXXXXXX',
    'recipient_address' => 'House 123, Road 4, Dhaka',
    'delivery_type' => {{48 for Normal Delivery, 12 for On Demand Delivery}},
    'item_type' => {{1 for Document, 2 for Parcel}},
    'item_quantity' => {{Quantity of your parcels}},
    'item_weight' => {{Minimum 0.5 KG to Maximum 10 KG}},
    'item_description' => 'Test item',
    'amount_to_collect' => {{Recipient Payable Amount. Default should be 0 in case of NON Cash-On-Delivery(COD)The collectible amount from the customer.}},
]);
```

#### Bulk Order

```php
$orders = [$order1, $order2];
$response = Pathao::createBulkOrder($orders);
```

#### Order Info

```php
$orderInfo = Pathao::orderInfo($consignmentId);
```

### Price Calculator

```php
$price = Pathao::priceCalculator([
    'store_id' => {{merchant_store_id}},
    'item_type' => {{1 for Document, 2 for Parcel}},
    'delivery_type' => {{48 for Normal Delivery, 12 for On Demand Delivery}},
    'item_weight' => {{Minimum 0.5 KG to Maximum 10 KG}},
    'recipient_city' => {{city_id}},
    'recipient_zone' => {{zone_id}},
]);
```

## Requirements

- Laravel 8-12
- PHP 8.2+

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
