# Sift Science PHP Bindings <a href="https://travis-ci.org/SiftScience/sift-php"><img src="https://travis-ci.org/SiftScience/sift-php.svg?branch=master">

## Installation
### With Composer
1. Add siftscience/sift-php as a dependency in composer.json.

    ```
    "require": {
        ...
        "siftscience/sift-php" : "1.*"
        ...
    }
    ```

2. Run `composer update`.
3. Now `SiftClient` will be autoloaded into your project.


    ```
    require 'vendor/autoload.php';

    $sift = new SiftClient('my_api_key');
    ```

### Manually
1. Download the latest release.
2. Extract into a folder in your project root named "sift-php".
2. Include `SiftClient` in your project like this:

    ```
    require 'sift-php/lib/Services_JSON-1.0.3/JSON.php';
    require 'sift-php/lib/SiftRequest.php';
    require 'sift-php/lib/SiftResponse.php';
    require 'sift-php/lib/SiftClient.php';
    require 'sift-php/lib/Sift.php';


    $sift = new SiftClient('my_api_key');
    ```

## Usage
### Track an event
Here's an example that sends a `$transaction` event to sift.

```
$sift = new SiftClient('my_api_key');
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
$sift = new SiftClient('my_api_key');
$response = $sift->label('23056', array(
    '$is_bad' => true,
    '$reasons' => array('$chargeback')
));
```
### Unlabel a user

```
$sift = new SiftClient('my_api_key');
$response = $sift->unlabel('23056');
```
### Get a user's score

```
$sift = new SiftClient('my_api_key');
$response = $sift->score('23056');
$response->body['score']; // => 0.030301357270181357
```


## Contributing
Run the tests from the project root with [PHPUnit](http://phpunit.de) like this:

```
phpunit --bootstrap vendor/autoload.php test/SiftClientTest
```

## Updating Packagist

1. Update `composer.json` to reflect the new version, as well as any new requirements then merge changes into master.

2. Create a [new release](https://github.com/SiftScience/sift-php/releases) with the version number and use it as the description.
[Packagist](https://packagist.org/packages/siftscience/sift-php) will automatically deploy a new package via the configured webhook.

## License
MIT
