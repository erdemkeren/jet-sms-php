# PHP JetSMS Client

Bu paket, hem XML hem Http API ile çalışan kullanımı kolay bir JetSMS servisi sağlar.

This package provides an easy to use JetSMS service which can be used with both XML and Http apis.
For the English version: [README](README.md)

## Contents

- [Kurulum](#kurulum)
    - [JetSMS Servisinin Kurulmasi](#jetsms-servisinin-kurulmasi)
- [Kullanim](#kullanim)
    - [Metotlar](#metotlar)
- [Degisiklik Listesi](#degisiklik-listesi)
- [Test](#test)
- [Guvenlik](#guvenlik)
- [Katkida Bulunmak](#katkida-bulunmak)
- [Jenerik](#jenerik)
- [Lisans](#lisans)

## Kurulum

Bu paket, composer kullanılarak kurulabilir.

``` bash
composer require erdemkeren/jet-sms-php
```

### JetSMS Servisinin Kurulmasi

JetSMS servisini kullanabilmek için kayıt olunmalı ve kontör satın alınmalı. 

## Kullanim

Önce, JetSmsService sınıfı, istenilen istemci uyarlaması kullanarak çalıştırılır.

- **JetSmsXmlClient**
- **JetSmsHttpClient** (Bu daha ziyade Rest servisi gibi ama HTTP demeyi tercih etmiş.)

```php
require __DIR__ . '/../vendor/autoload.php';

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

### Metotlar

JetSmsService örneğini başarıyla çalıştırdıktan sonra; aşağıda bulunan metotlardan birini kullanarak SMS(ler) göndermeye başlayabilirsiniz.

#### Tek Mesaj - Bir ya da Daha Çok Alıcı

```php
$response = $service->sendShortMessage('Bu bir test mesajıdır.', ['5530000000', '5420000000']);

if($response->isSuccessful()) {
    // storeGroupIdForLaterReference fonksiyonu pakete dahil değildir.
    storeGroupIdForLaterReference($response->groupId());
} else {
    var_dump($response->message());
    var_dump($response->statusCode());
    var_dump($response->status());
}
```

#### Çoklu Mesaj - Çoklu Alıcı:

Eğer bu yöntemi kullanıyorsanız, her mesajın yalnızca bir alıcısı olmalıdır. _(Bu da hacklemediğim bir API kısıtıdır.)_

```php
$response2 = $service->sendShortMessages([[
    'body' => 'This is a test.',
    'mobile_number' => '5530000000',
], [
    'body' => 'This is another test.',
    'mobile_number' => '5420000000',
]]);

if($response2->isSuccessful()) {
    // storeGroupIdForLaterReference fonksiyonu pakete dahil değildir.
    storeGroupIdForLaterReference($response2->groupId());
} else {
    var_dump($response2->message());
    var_dump($response2->statusCode());
    var_dump($response2->status());
}
```

### Dipnot

Eğer istemci olarak `JetSmsHttpClient` sınıfı kullanılıyorsa `$response->groupId()` çağrısı istisnaya sebep olur.
Eğer istemci olarak `JetSmsXmlClient` sınıfı kullanılıyorsa `$response->messageReportIdentifiers()` çağrısı istisnaya sebep olur.

İstemci uyarlamasını değiştirirken temkinli olun.

## Degisiklik Listesi

Lütfen son değişiklikleri görmek için [Değişiklik Listesi](DEGISIKLIKLER.md) dosyasını ziyaret ediniz.


## Test

``` bash
$ composer test
```

## Güvenlik

Bu paket, JetSMS tarafından sağlanan servisleri kullanmaktadır. Eğer istemci taraflı bir güvenlik açığı bulduysanız; lütfen
yeni bir ticket açmak yerine geliştiriciye e-posta atın.

## Katkıda Bulunun

Eğer katkıda bulunmak isterseniz lütfen [Katkıda Bulunun](KATKI.md) dosyasını inceleyin.

## Tanıtımlar

- [Hilmi Erdem KEREN](https://github.com/erdemkeren)
- [Uğur Aydogdu](https://github.com/jnbn)

Bu paket

[epigra/tckimlik](https://github.com/epigra/tckimlik) paketinin üzerine geliştirilmiştir.

## Lisans

The MIT License (MIT). Detaylar için lütfen [Lisans Dosyasını](LISANS.md) inceleyin.