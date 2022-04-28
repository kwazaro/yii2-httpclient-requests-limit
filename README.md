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
<?= \kwazaro\yii2\httpclient\AutoloadExample::widget(); ?>```