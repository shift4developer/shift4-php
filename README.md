# Shift4 PHP Library

If you don't already have Shift4 account you can create it [here](https://dev.shift4.com/signup). 

## Installation 

### Composer

Best way to use this library is via [Composer](http://getcomposer.org/).

```
composer require shift4/shift4-php
```


Then to use the library, you can use Composer's autoloader:

```php
require_once('vendor/autoload.php');
```

### Manual installation

If you don't want to use Composer then you can download [the latest release](https://github.com/shift4developer/shift4-php/releases).

Then to use the library, you can either configure your autoloader to load classes from the `lib/` directory or use included autoloader:

```php
 require_once 'lib/Shift4/Util/Shift4Autoloader.php';
 \Shift4\Util\Shift4Autoloader::register();
```

## Quick start example

```php
use Shift4\Shift4Gateway;
use Shift4\Exception\Shift4Exception;

$gateway = new Shift4Gateway('sk_test_[YOUR_SECRET_KEY]');

$request = [
    'amount' => 499,
    'currency' => 'EUR',
    'card' => [
        'number' => '4242424242424242',
        'expMonth' => 11,
        'expYear' => 2022
    ]
];

try {
    $charge = $gateway->createCharge($request);

    // do something with charge object - see https://dev.shift4.com/docs/api#charge-object
    $chargeId = $charge->getId();

} catch (Shift4Exception $e) {
    // handle error response - see https://dev.shift4.com/docs/api#error-object
    $errorType = $e->getType();
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
}
```

## Documentation

For further information, please refer to our official documentation at [https://dev.shift4.com/docs](https://dev.shift4.com/docs).
