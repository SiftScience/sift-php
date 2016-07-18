# Sift Science PHP Bindings <a href="https://travis-ci.org/SiftScience/sift-php"><img src="https://travis-ci.org/SiftScience/sift-php.svg?branch=master">


## Installation

### With Composer

1. Add siftscience/sift-php as a dependency in composer.json.

    ```
    "require": {
        ...
        "siftscience/sift-php" : "2.*"
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
    require 'sift-php/lib/Services_JSON-1.0.3/JSON.php';
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
    '$seller_user_email' => 'seller@gmail.com',
    '$transaction_id' => '573050',
    '$currency_code' => 'USD',
    '$amount' => 15230000,
    '$time' => 1327604222,
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

## Contributing
Run the tests from the project root with [PHPUnit](http://phpunit.de) like this:

```
phpunit --bootstrap vendor/autoload.php test
```


## Updating Packagist

1. Update `composer.json` to reflect the new version, as well as any
   new requirements then merge changes into master.

2. Create a [new release](https://github.com/SiftScience/sift-php/releases)
    with the version number and use it as the description.
    [Packagist](https://packagist.org/packages/siftscience/sift-php) will
    automatically deploy a new package via the configured webhook.


## License

MIT
