# Sift Bindings [![CircleCI](https://circleci.com/gh/SiftScience/sift-php.svg?style=svg)](https://circleci.com/gh/SiftScience/sift-php)


## Installation

### With Composer

1. Add siftscience/sift-php as a dependency in composer.json.

    ```
    "require": {
        ...
        "siftscience/sift-php" : "4.*"
        ...
    }
    ```

2. Run `composer update`.

3. Now `SiftClient` will be autoloaded into your project.


    ```
    require 'vendor/autoload.php';

    $sift = new SiftClient(array(
        'api_key' => 'my_api_key',
        'account_id' => 'my_account_id'
    ));

    // or

    Sift::setApiKey('my_api_key');
    Sift::setAccountId('my_account_id');
    $sift = new SiftClient();
    ```

### Manually

1. Download the latest release.

2. Extract into a folder in your project root named "sift-php".

3. Include `SiftClient` in your project like this:

    ```
    require 'sift-php/lib/SiftRequest.php';
    require 'sift-php/lib/SiftResponse.php';
    require 'sift-php/lib/SiftClient.php';
    require 'sift-php/lib/Sift.php';


    $sift = new SiftClient(array(
        'api_key' => 'my_api_key',
        'account_id' => 'my_account_id'
    ));
    ```


## Usage

### Track an event
Here's an example that sends a `$transaction` event to sift.
```
$sift = new SiftClient(array('api_key' => 'my_api_key'));
$response = $sift->track('$transaction', array(
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
));
```

### Label a user as good/bad
```
$sift = new SiftClient(array('api_key' => 'my_api_key'));
$response = $sift->label('23056', array(
    '$is_bad' => true,
    '$abuse_type' => 'promotion_abuse'
));
```

### Unlabel a user
```
$sift = new SiftClient(array('api_key' => 'my_api_key'));
$response = $sift->unlabel('23056', array('abuse_type' => 'content_abuse'));
```

### Get a user's score
```
$sift = new SiftClient(array('api_key' => 'my_api_key'));
$response = $sift->score('23056');
$response->body['scores']['payment_abuse']['score']; // => 0.030301357270181357
```

### Get the status of a workflow run
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->getWorkflowStatus('my_run_id');
$response->body['state']; // => "running"
```

### Get the latest decisions for a user
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->getUserDecisions('example_user');
$response->body['decisions']['account_abuse']['decision']['id']; // => "ban_user"
```

### Get the latest decisions for an order
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->getOrderDecisions('example_order');
$response->body['decisions']['payment_abuse']['decision']['id']; // => "ship_order"
```

### Get the latest decisions for a session
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->getSessionDecisions('example_user', 'example_session');
$response->body['decisions']['account_takeover']['decision']['id']; // => "session_decision"
```

### List of configured Decisions
**Optional Params**
 - `entity_type`: `user` or `order` or `session`
 - `abuse_types`: `["payment_abuse", "content_abuse", "content_abuse",
   "account_abuse", "legacy", "account_takeover"]`

```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $this->client->getDecisions(array('entity_type' => 'example_entity_type','abuse_types' => 'example_abuse_types'));
$response->isOk()
```

### Apply decision to a user
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->applyDecisionToUser('example_user','example_decision','example_source',array('analyst' => 'analyst@example.com')
$response->isOk()
```

### Apply decision to an order
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->applyDecisionToOrder('example_user','example_order','example_decision','example_source',array('analyst' => 'analyst@example.com')
$response->isOk()
```

### Apply decision to a session
```
$sift = new SiftClient(array('api_key' => 'my_api_key', 'account_id' => 'my_account_id'));
$response = $sift->applyDecisionToSession('example_user','example_session','example_decision','example_source',array('analyst' => 'analyst@example.com')
$response->isOk()
```

## Contributing
Run the tests from the project root with [PHPUnit](http://phpunit.de) like this:

```
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
Because standard PHP/fastcgi deployments don't have a mechanisms for persisting connections between
requests, the easiest way to pool connections is by running
[Apache httpd](https://httpd.apache.org/) as a proxy.

```
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

And instantiating `SiftClient` to route requests through it:

```php
$sift = new SiftClient(array(
    'api_key' => 'my_api_key',
    'account_id' => 'my_account_id',
    'api_endpoint' => 'http://api.sift.com',
    'curl_opts' => array(CURLOPT_CONNECT_TO => array('api.sift.com:80:localhost:8081')),
));
```

## License

MIT
