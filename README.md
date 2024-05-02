# Sift Bindings [![CircleCI](https://circleci.com/gh/SiftScience/sift-php.svg?style=svg)](https://circleci.com/gh/SiftScience/sift-php)



## Installation

### With Composer

1. Add siftscience/sift-php as a composer dependency

```shell script
composer require siftscience/sift-php
```

2. Now `SiftClient` will be autoloaded into your project.

```php
require 'vendor/autoload.php';

$sift = new SiftClient([
    'api_key' => 'my_api_key',
    'account_id' => 'my_account_id'
]);

// or

Sift::setApiKey('my_api_key');
Sift::setAccountId('my_account_id');
$sift = new SiftClient();
```


### Manually

1. Download the latest release.

2. Extract into a folder in your project root named "sift-php".

3. Include `SiftClient` in your project like this:

    ```php
    require 'sift-php/lib/SiftRequest.php';
    require 'sift-php/lib/SiftResponse.php';
    require 'sift-php/lib/SiftClient.php';
    require 'sift-php/lib/Sift.php';


    $sift = new SiftClient([
        'api_key' => 'my_api_key',
        'account_id' => 'my_account_id'
    ]);
    ```


## Usage

### Track an event
Here's an example that sends a `$transaction` event to sift.
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->track('$transaction', [
    '$user_id' => '23056',
    '$user_email' => 'buyer@gmail.com',
    '$seller_user_id' => '2371',
    '$transaction_id' => '573050',
    '$currency_code' => 'USD',
    '$amount' => 15230000,
    '$time' => 1327604222,
    'seller_user_email' => 'seller@gmail.com',
    'trip_time' => 930,
    'distance_traveled' => 5.26,
]);
```

### Label a user as good/bad
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->label('23056', [
    '$is_bad' => true,
    '$abuse_type' => 'promotion_abuse'
]);
```

### Unlabel a user
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->unlabel('23056', ['abuse_type' => 'content_abuse']);
```

### Get a user's score
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->score('23056');
$response->body['scores']['payment_abuse']['score']; // => 0.030301357270181357
```

### Get the status of a workflow run
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->getWorkflowStatus('my_run_id');
$response->body['state']; // => "running"
```

### Get the latest decisions for a user
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->getUserDecisions('example_user');
$response->body['decisions']['account_abuse']['decision']['id']; // => "ban_user"
```

### Get the latest decisions for an order
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->getOrderDecisions('example_order');
$response->body['decisions']['payment_abuse']['decision']['id']; // => "ship_order"
```

### Get the latest decisions for a session
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->getSessionDecisions('example_user', 'example_session');
$response->body['decisions']['account_takeover']['decision']['id']; // => "session_decision"
```

### List of configured Decisions
**Optional Params**
 - `entity_type`: `user` or `order` or `session`
 - `abuse_types`: `["payment_abuse", "content_abuse", "content_abuse",
   "account_abuse", "legacy", "account_takeover"]`

```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $this->client->getDecisions(['entity_type' => 'example_entity_type','abuse_types' => 'example_abuse_types']);
$response->isOk()
```

### Apply decision to a user
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->applyDecisionToUser('example_user','example_decision','example_source',['analyst' => 'analyst@example.com']
$response->isOk()
```

### Apply decision to an order
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->applyDecisionToOrder('example_user','example_order','example_decision','example_source',['analyst' => 'analyst@example.com']
$response->isOk()
```

### Apply decision to a session
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->applyDecisionToSession('example_user','example_session','example_decision','example_source',['analyst' => 'analyst@example.com']
$response->isOk()
```
### Creates a new webhook with a specified URL.
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->postWebhooks(["payload_type" => "ORDER_V1_0",
    "status"=> "active",
    "url"=> "https://example1.com/",
    "enabled_events" => ['$create_order'],
    "name"=> "My webhook name",
    "description"=> "This is a webhook!"]);
$response->isOk()
```
### Retrieves a webhook when given an ID.
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->retrieveWebhook('webhook_id');
$response->isOk()
```
### List All Webhooks
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->listAllWebhooks();
$response->isOk()
```
### Update a Webhook
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->updateWebhook('webhook_id', ["payload_type" => "ORDER_V1_0",
    "status"=> "active",
    "url"=> "https://example1.com/",
    "enabled_events" => ['$create_order'],
    "name"=> "My webhook name update",
    "description"=> "This is a webhook! update"]);
$response->isOk()
```
### Deletes a webhook when given an ID.
```php
$sift = new SiftClient(['api_key' => 'my_api_key', 'account_id' => 'my_account_id']);
$response = $sift->deleteWebhook('webhook_id');
$response->isOk()
```

## Contributing
Run the tests from the project root with [PHPUnit](http://phpunit.de) like this:

```php
composer update
composer exec phpunit -v -- --bootstrap vendor/autoload.php test
```


## Updating Packagist

1. Update `composer.json` to reflect the new version, as well as any
   new requirements then merge changes into master.

2. Create a [new release](https://github.com/SiftScience/sift-php/releases)
    with the version number and use it as the description.
    [Packagist](https://packagist.org/packages/siftscience/sift-php) will
    automatically deploy a new package via the configured webhook.

## HTTP connection pooling

You can substantially improve the performance of `SiftClient` by using HTTP connection pooling.
Because standard PHP/fastcgi deployments don't have a mechanism for persisting connections between
requests, the easiest way to pool connections is by routing requests through a proxy like Apache httpd or nginx.

**[Apache httpd](https://httpd.apache.org/)**

```apache
Listen 8081

...

LoadModule proxy_module .../mod_proxy.so
LoadModule proxy_http_module .../mod_proxy_http.so
LoadModule ssl_module .../mod_ssl.so

<VirtualHost localhost:8081>
    ServerName api.sift.com
    SSLProxyEngine on
    SSLProxyVerify require
    SSLProxyVerifyDepth 3
    SSLProxyCACertificateFile ...
    ProxyPass / https://api.sift.com/
</VirtualHost>
```

**[nginx](https://www.nginx.com/)**

```nginx
# Read more about nginx keepalive: https://www.nginx.com/blog/tuning-nginx/#keepalive
upstream sift {
    server api.sift.com:443;
    keepalive 16;
}

server {
    listen localhost:8081;
    server_name api.sift.com;

    location / {
        proxy_pass https://sift;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_ssl_verify on;
        proxy_ssl_verify_depth 3;
        proxy_ssl_trusted_certificate ...;
        proxy_ssl_name api.sift.com;
        proxy_ssl_server_name on;
    }
}
```

For Debian-based distributions, the certificate file is `/etc/ssl/certs/ca-certificates.crt`

Then, instantiate `SiftClient` to route requests through the proxy:

```php
$sift = new SiftClient([
    'api_key' => 'my_api_key',
    'account_id' => 'my_account_id',
    'api_endpoint' => 'http://api.sift.com',
    'curl_opts' => [CURLOPT_CONNECT_TO => ['api.sift.com:80:localhost:8081']],
]);
```

## Integration testing app

For testing the app with real calls it is possible to run the integration testing app,
it makes calls to almost all our public endpoints to make sure the library integrates
well. At the moment, the app is run on every merge to master

#### How to run it locally

1. Add env variable `ACCOUNT_ID` with the valid account id
2. Add env variable `API_KEY` with the valid Api Key associated from the account
3. Run the following under the project root folder
```
# install the lib from the local source code
composer install -n
# run the app
php test_integration_app/main.php
```

## License

MIT
