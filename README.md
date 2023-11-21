<h1>petervdbroek/ideal2</h1>

<p>
    <strong>A PHP library for working with iDEAL 2.0 payments using the Open Banking APIs</strong>
</p>

## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require petervdbroek/ideal2
```

## Status
This package is currently under development. <br />
The only supported bank is <strong>Rabobank</strong>. More banks can be added in the future.

## Documentation

### Initiate library
```php
$ideal = new RabobankiDEAL('<merchantId>', '<certificatePath>', '<privateKeyPath>', '<env:test|prod>');
```

### Initiate a payment
```php
$payment = $ideal->createPayment(<amount>, '<reference>', '<notificationUrl>', '<returnUrl>');
```
This will return a [Payment][] object containing a Payment ID.

### Get payment status
```php
$paymentStatus = $ideal->getPaymentStatus(<paymentId>);
```
This will return a [Payment][] object containing the status.

## Copyright and License

The petervdbroek/ideal2 library is copyright © Peter van den Broek and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.

[composer]: http://getcomposer.org/
[license]: https://github.com/petervdbroek/ideal2/blob/main/LICENSE
[payment]: https://github.com/petervdbroek/ideal2/blob/main/src/Resources/Payment.php