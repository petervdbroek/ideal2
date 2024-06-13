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
This package is a minimal viable product for doing **standard** payments with iDEAL 2.0 using the Open Banking APIs.\
There is an open TODO in the [Signer][] class to add verification of Signature and Digest in Responses and on notifications. This will be added later.\
This package currently does not support Fast Checkout or Profile recognition via Debtor Tokens.\
If you need this functionality you can add this by creating a PR on this repo. See the [Contribute](#a-namecontributeacontribute) section.

## Documentation

### Initiate library
```php
$ideal = new iDEAL('<merchantId>', '<client>', '<baseUri>', '<certificatePath>', '<privateKeyPath>', '<publicCertificateFilePath>');
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
This will return a [PaymentStatus][] object containing the status.

## <a name="contribute"></a>Contribute
If you need more functionality you can create a PR on this repo.\
You can extend the [Resources][] by adding new getters, and extend or add [Endpoints][] to add more functionality like Fast Checkout or Profile recognition via Debtor Tokens.

## Copyright and License

The petervdbroek/ideal2 library is copyright © Peter van den Broek and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.

[composer]: http://getcomposer.org/
[license]: https://github.com/petervdbroek/ideal2/blob/main/LICENSE
[signer]: https://github.com/petervdbroek/ideal2/blob/main/src/Utils/Signer.php#L114
[payment]: https://github.com/petervdbroek/ideal2/blob/main/src/Resources/Payment.php
[paymentstatus]: https://github.com/petervdbroek/ideal2/blob/main/src/Resources/PaymentStatus.php
[resources]: https://github.com/petervdbroek/ideal2/blob/main/src/Resources
[endpoints]: https://github.com/petervdbroek/ideal2/blob/main/src/Endpoints
