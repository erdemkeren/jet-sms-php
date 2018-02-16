# PHP JetSMS Client

This package provides an easy to use JetSMS service which can be used with both XML and Http apis.

Bu paket, hem XML hem Http API ile çalışan kullanımı kolay bir JetSMS servisi sağlar.

Dokümanın türkçe hali için: [BENIOKU](BENIOKU.md)

## Contents

- [Installation](#installation)
    - [Setting up the JetSMS service](#setting-up-the-jetsms-service)
- [Usage](#usage)
    - [Available methods](#available-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install this package via composer:

``` bash
composer require erdemkeren/jet-sms-php
```

### Setting up the JetSMS service

You will need to register to JetSMS to use this channel.

## Usage

First, boot the JetSmsService with your desired client implementation.
- **JetSmsXmlClient**
- **JetSmsHttpClient** (This is actually a Rest-Like client but the vendor names their API that way.)

```php
require __DIR__ . '/../vendor/autoload.php';

use Erdemkeren\JetSms\JetSmsService;
use Erdemkeren\JetSms\JetSmsService;
use Erdemkeren\JetSms\ShortMessageFactory;
use Erdemkeren\JetSms\Http\Clients\JetSmsXmlClient;
use Erdemkeren\JetSms\Http\Clients\JetSmsHttpClient;
use Erdemkeren\JetSms\ShortMessageCollectionFactory;

$service = new JetSmsService(new JetSmsXmlClient(
    'www.biotekno.biz:8080/SMS-Web/xmlsms',
    'username',
    'password',
    'outboxname'
), new ShortMessageFactory(), new ShortMessageCollectionFactory());

// ya da

$service = new JetSmsService(new JetSmsHttpClient(
    new GuzzleHttp\Client(),
    'https://service.jetsms.com.tr/SMS-Web/HttpSmsSend',
    'username',
    'password',
    'outboxname'
), new ShortMessageFactory(), new ShortMessageCollectionFactory());
```

### Available methods

After successfully booting your JetSmsService instance up; use one of the following methods to send SMS message(s).

#### One Message - Single or Multiple Recipients:

```php
$response = $service->sendShortMessage('This is a test message.', ['5530000000', '5420000000']);

if($response->isSuccessful()) {
    // storeGroupIdForLaterReference is not included in the package.
    storeGroupIdForLaterReference($response->groupId());
} else {
    var_dump($response->message());
    var_dump($response->statusCode());
    var_dump($response->status());
}
```

#### Multiple Messages - Multiple Recipients:

Please not that if you have using that method, every message should only have one receiver. _(This is also an API limitation which I didn't hack.)_

```php
$response2 = $service->sendShortMessages([[
    'body' => 'This is a test.',
    'mobile_number' => '5530000000',
], [
    'body' => 'This is another test.',
    'mobile_number' => '5420000000',
]]);

if($response2->isSuccessful()) {
    // storeGroupIdForLaterReference is not included in the package.
    storeGroupIdForLaterReference($response2->groupId());
} else {
    var_dump($response2->message());
    var_dump($response2->statusCode());
    var_dump($response2->status());
}
```

### Cross Reference

`$response->groupId()` will throw BadMethodCallException if the client is `JetSmsHttpClient`.
`$response->messageReportIdentifiers()` will throw BadMethodCallException if the client is `JetSmsXmlClient`.

change client implementation with caution.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email erdemkeren@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Hilmi Erdem KEREN](https://github.com/erdemkeren)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
