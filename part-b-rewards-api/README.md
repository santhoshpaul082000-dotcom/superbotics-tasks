# SuperBotics – Rewards Redemption API

## Tech Stack
- Laravel 12
- PHP 8.2
- MySQL
- React.js
- Postman

## Setup Instructions

1. Clone the repository.
2. Navigate to `part-b-rewards-api`.
3. Run `composer install`.
4. Copy `.env.example` to `.env`.
5. Configure MySQL database credentials.
6. Run `php artisan key:generate`.
7. Run `php artisan migrate:fresh --seed`.
8. Start the API using `php artisan serve`.

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | /api/redemptions | Redeem a product |
| GET | /api/users/{id}/balance | Check points balance |
| GET | /api/reports/redemptions | View redemption report |

## Double-Spending Prevention

The redemption service uses a database transaction and row-level locking on the user record. This prevents simultaneous redemption requests for the same user from spending the same points.

## Report Query and Index

The report calculates total redemption count and points using SQL aggregate functions: COUNT and SUM.

An index on created_at and category_id helps filter report records.

## Part C – Offline Scenario

Store offline redemptions locally with unique transaction IDs. When internet returns, sync them to the server. Use idempotency keys and database transactions to prevent duplicate redemptions. Check the user's available points during synchronization.

Possible problems include duplicate requests, insufficient points, and synchronization conflicts.

## Part D – Product Improvements

1. Add redemption history so users can track their points.
2. Send notifications after successful redemption.
3. Add an admin dashboard to manage products and redemption limits.

## AI Log

1. Used AI to understand Laravel migrations and relationships.
2. Used AI to implement the redemption service and API controllers.
3. Used AI to understand Git commands and API testing.

One issue: AI initially suggested an empty service method, which returned null. I identified this by checking the API response and then implemented the redemption logic.

## Learning Log

- Learned Laravel service classes and Form Request validation.
- Learned database transactions and row locking.
- Learned how to test APIs using Postman.
- The most challenging part was implementing redemption rules and preventing double-spending.

## Current Status

The backend APIs have been implemented and tested manually. Feature tests and React frontend are still pending.