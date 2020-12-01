# Payment Package for Laravel

This is a Laravel Package for Payment Gateway Integration.

## Installation

#### Composer

```javascript
composer require ggphp/laravel-payment
```
## Configuration

#### Config File

Publish the default config file `payment.php` to your application

```console
php artisan config:publish --force
```
Then choose `payment-config`
#### Run Migrations

```console
php artisan migrate
```
#### Customer Model Setup

Next, add the CustomerBillableTrait to your customer model definition:

```php
use GGPHP\Payment\CustomerPaymentTrait;

class User extends Eloquent
{
  use CustomerPaymentTrait;
}
```
## Customers

#### Creating A Customer

Once you have a customer model instance, you can create simple the customer in the billing gateway:

```php
$user = User::find(1);

$user->payment()->create();
```
If you would like create customer with properties:

```php
$user->payment()->create([
  'email' => $email,
]);
```
#### Updating A Customer

To update an existing customers:
```php
$user->payment()->update([
  'email' => $email,
]);
```
#### Deleting A Customer

Deleting a customer:

```php
$user->payment()->delete();
```
#### Create Credit Cards

You may add more than one credit card on a customer:

```php
$card = $user->card()->create('credit_card_token');
```
#### Create A Charge

Creating a new charge on a customer:

```php
$charge = $user->charges()->create(5996, ['description' => 'description']);
```
## Cards

#### Creating Card on a Customer

Once you have a customer model instance, you can create simple the card:

```php
$user = User::find(1);

$card = $user->card()->create('credit_card_token');
```
#### Get all cards on a Customer

Get all cards on a Customer:

```php
$cards $user->card()->all();
```
#### Get first card on a Customer

Get first card on a Customer:

```php
$card = $user->card()->first();
```
#### Find A Card

To find an existing card:

```php
$card = $user->card()->find('id_card');
```
#### Update card on a Customer

To update an existing card:
```php
$card = $card->update([
  'exp_month' => '1'
]);
```
#### Delete A Card

To delete an existing card:

```php
$card->delete();
```
## Charge

#### Creating A Charge

Creating a new charge on a customer:

```php
$charge = $user->charges()->create(499);
```
If you would like create charge with properties:

```php
$user->charges()->create(499, [
  'email' => $email,
]);
```
To charge on a new credit card token:

```php
$charge = $user->charges()->withCardToken('token')->create(499);
```

You may also specify an existing credit card to use for a charge:

```php
$charge = $user->charges()->withCard('card_id')->create(499);
```
#### Capturing A Charge

 To Capturing A Charge:

```php
$charge = $user->charges()->create(499, array('capture' => false));

$charge->capture();
```
#### Refunding A Charge

Refunding a charge is also possible:

```php
$charge->refund();
```
If you would like refund charge with properties:

```php
$charge->refund([
  'amount' => 399,
  'reason' => 'reason'
]);
```
#### Get all charges on a Customer

Get all charges on a Customer:

```php
$cards $user->charges()->all();
```
#### Get first charges on a Customer

Get first card on a Customer:

```php
$card = $user->charges()->first();
```
#### Update charge

To update an existing charge:
```php
$card = $charge->update([
  'description' => 'description'
]);
```
