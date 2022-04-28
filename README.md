Yii2 Http Client requests limit behavior
========================================
This is the behavior for Yii2 Http Client, which provides rate limiting for requests per second or per minute. It is useful for connecting web services with rate limiting.
This behavior uses Yii2 Redis extension to store rate-limiting info.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kwazaro/yii2-httpclient-requests-limit "*"
```

or add

```
"kwazaro/yii2-httpclient-requests-limit": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?php
use kwazaro\yii2\httpclient\HttpClientRateLimitBehavior;

$client = new \yii\httpclient\Client([
    'baseUrl' => 'https://example.com/api',
]);
$client->attachBehavior('requestsLimit', [
    'class' => HttpClientRateLimitBehavior::class,
    'redis' => 'redis', // ID of your Yii2 Redis component.
    'redisKey' => 'myRequests', // Name of Redis key for storing data.
    'maxRequestsPerSecond' => 10, // Max number of requests per second.
    'maxRequestsPerMinute' => 100, // Max number of requests per minute.
]);