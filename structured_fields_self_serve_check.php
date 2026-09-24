<?php
/**
 * NEB-4783 QA self-serve check, section D (client libraries) - PHP.
 *
 * IMPORTANT: none of the 6 client library PRs (Java, .NET, Python, PHP, Ruby) are merged
 * or released yet. This only works against a local checkout of this branch - installing
 * the published sift-php package via Composer will NOT have these fields.
 *
 * The PHP client does zero field validation - it's a thin pass-through that serialises
 * whatever array you give it. So there's no "compile-time safety" story here like
 * Java/.NET; this script only proves the round trip against prod actually works.
 *
 * This deliberately does NOT go through Composer/PHPUnit - this repo's composer.json
 * pins an old PHPUnit version incompatible with modern PHP, so we just require the
 * lib files directly (same approach used during the original implementation work).
 *
 * How to run:
 *   SIFT_QA_API_KEY=... php structured_fields_self_serve_check.php
 *
 * Each call reuses the exact payload shapes already verified in section A's Postman
 * collection. All 7 should print "apiStatus: 0" - if any doesn't, that's a real finding.
 */

require_once __DIR__ . '/lib/Sift.php';
require_once __DIR__ . '/lib/SiftResponse.php';
require_once __DIR__ . '/lib/SiftRequest.php';
require_once __DIR__ . '/lib/SiftClient.php';

const USER_ID = 'qa_structured_fields_2026';
const ORDER_ID = 'qa_order_001';

$apiKey = getenv('SIFT_QA_API_KEY');
if (!$apiKey) {
    fwrite(STDERR, "Set SIFT_QA_API_KEY first - see the comment at the top of this file.\n");
    exit(1);
}

$client = new SiftClient(['api_key' => $apiKey, 'account_id' => USER_ID]);

function check($client, $label, $event, $properties) {
    $response = $client->track($event, $properties);
    echo "[$label] httpStatusCode={$response->httpStatusCode} " .
         "apiStatus={$response->apiStatus} " .
         "apiErrorMessage={$response->apiErrorMessage}\n";
    if ($response->apiStatus !== 0 || $response->httpStatusCode !== 200) {
        echo "[$label] UNEXPECTED - expected apiStatus 0 / http 200\n";
    }
}

check($client, 'create_account', '$create_account', [
    '$user_id' => USER_ID,
    '$nationality' => 'US',
    '$year_of_birth' => 1985,
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$basic',
        '$bin_nationality_match' => true,
        '$provider' => 'lexisnexis',
    ],
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'datadome'],
]);

check($client, 'update_account', '$update_account', [
    '$user_id' => USER_ID,
    '$nationality' => 'US',
    '$year_of_birth' => 1985,
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$full',
        '$bin_nationality_match' => true,
        '$provider' => 'lexisnexis',
    ],
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'datadome'],
]);

check($client, 'login', '$login', [
    '$user_id' => USER_ID,
    '$login_status' => '$success',
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'datadome'],
]);

check($client, 'transaction', '$transaction', [
    '$user_id' => USER_ID,
    '$amount' => 15230000,
    '$currency_code' => 'USD',
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$full',
        '$bin_nationality_match' => false,
        '$provider' => 'prove',
    ],
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'human_security'],
]);

check($client, 'create_order', '$create_order', [
    '$user_id' => USER_ID,
    '$order_id' => ORDER_ID,
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$basic',
        '$bin_nationality_match' => true,
        '$provider' => 'lexisnexis',
    ],
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'datadome'],
]);

check($client, 'update_order', '$update_order', [
    '$user_id' => USER_ID,
    '$order_id' => ORDER_ID,
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$basic',
        '$bin_nationality_match' => true,
        '$provider' => 'lexisnexis',
    ],
    '$geo' => ['$uuid' => 'gc-abc-123', '$provider' => 'geocomply'],
    '$bot_identification' => ['$result' => '$human', '$provider' => 'datadome'],
]);

// No $geo/$bot_identification here on purpose - $verification only supports $kyc per
// the attachment matrix.
check($client, 'verification', '$verification', [
    '$user_id' => USER_ID,
    '$verification_type' => '$kyc',
    '$status' => '$success',
    '$kyc' => [
        '$names_match' => true,
        '$kyc_level' => '$basic',
        '$bin_nationality_match' => false,
        '$provider' => 'lexisnexis',
    ],
]);
