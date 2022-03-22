# About Project
This project is using latest stable laravel version for now which is version 8.75.

All of features that i used inside this project is fully using laravel framework supported packages.
* For time manipulation, im using carbon.
* For ORM im using Eloquent
* For model transformer to be returned to response, im using laravel model resources
* for Token Authentication, im using Laravel Sanctum
* for request data validation, im using laravel build in validation
* for unit test and feature test im using phpunit that come within this laravel framework too

# Getting Started
Steps to run this app on your local machine:
* clone this repo
* make sure you have latest php and composer version, this project tested with `php version 7.4.3` and `composer version 2.1.10`
* create database for this project
* copy .env.example to .env
* put your db credential to newly created .env file
* `composer install`
* `php artisan migrate:fresh --seed`

after those steps completed, you can test this app.

I create a customer for <b>MANUAL</b> test purpose (inside CustomerSeeder).

```
username: testing
password: password
```

You can use above credential to test this app.

But if you want to run `Automation TEST` that i made, you can run
```
php artisan test
```
> for now im using mysql as database driver for unit test, but the best database for test's performance sake is sqlite3

There is four endpoints that already made :
* Login
* Logout
* Make Order
* Finish Existing Order

## Login ``(POST: /api/login)``
required parameters:
* username = `required|string`
* password = `required|string`

Error response :
* standart laravel error message

Success response example:
```
{
  "data": {
    "token": "1|YSqJ6SrM2dDEItOjhyJrudhCa1lLRmz7WHlKtb8G"
  }
}
```

> This token need to be used as Bearer Token on Authorization header for each requests that need an authentication (logout, make order, finish order)

## Logout ``(POST: /api/logout)``
> Bearer Token Needed

Error response :
* standart laravel error message

Success response example:
```
{
  "data": {
    "message": "success"
  }
}
```

## Make Order ``(POST: /api/orders)``
> Bearer Token Needed

required parameters:
* distance = `required|numeric|min:1`
* deadline = `required|date_format:d-m-Y H:i|after_or_equal:now (example '20-12-2021 15:45')`

Error response :
* standart laravel error message

Success response example:
```
{
  "data": {
    "order": {
      "customer_id": 1,
      "distance": 1,
      "deadline": "2022-03-23T16:38:00.000000Z",
      "assigned_pigeon_id": 1,
      "finished_time": null,
      "status": "on_going"
    }
  }
}
```

## Finish Existing Order ``(POST: /api/orders/{orderId}/finish)``
> Bearer Token Needed

required parameters:
* orderId = url parameter for selected order id, can only finish order that is belongs to logged in customer and order status should be `on_going`

Error response :
* standart laravel error message

Success response example:
```
{
  "data": {
    "messsage": "success"
  }
}
```
