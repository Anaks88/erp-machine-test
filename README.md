# Laravel 13 Mini ERP API

A mini ERP Purchase Order and Inventory Stock Management API built with Laravel 13 and PHP 8.3+.

## Features

- Supplier management
- Product management
- Purchase Order creation
- Automatic PO number generation
- Automatic subtotal calculation
- Automatic total calculation
- Purchase Order status state machine
- Inventory stock increment on RECEIVED
- Database transactions
- Form Request validation
- API Resources
- Automated tests

## Requirements

- PHP 8.3+
- Composer
- MySQL
- Laravel 13

## Installation

Clone the repository:

git clone <repository-url>

Enter the project:

cd erp-machine-test

Install dependencies:

composer install

Copy environment file:

copy .env.example .env

Generate application key:

php artisan key:generate

Configure database in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_machine_test
DB_USERNAME=root
DB_PASSWORD=

Run migrations and seeders:

php artisan migrate --seed

Start Laravel:

php artisan serve

## API Endpoints

GET /api/v1/products

POST /api/v1/purchase-orders

GET /api/v1/purchase-orders/{id}

PATCH /api/v1/purchase-orders/{id}/status

## Create Purchase Order

POST /api/v1/purchase-orders

Example:

{
    "supplier_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 10,
            "unit_price": 50.00
        },
        {
            "product_id": 2,
            "quantity": 5,
            "unit_price": 100.00
        }
    ]
}

## Approve Purchase Order

PATCH /api/v1/purchase-orders/1/status

{
    "status": "APPROVED"
}

## Receive Purchase Order

PATCH /api/v1/purchase-orders/1/status

{
    "status": "RECEIVED"
}

When a Purchase Order is marked as RECEIVED, the product stock is automatically incremented by the purchased quantity.

## Status Flow

DRAFT -> APPROVED -> RECEIVED

DRAFT -> CANCELLED

APPROVED -> CANCELLED

Once a Purchase Order is RECEIVED or CANCELLED, its status cannot be changed.

## Testing

Run:

php artisan test