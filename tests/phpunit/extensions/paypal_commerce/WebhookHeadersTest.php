<?php
/*
 * Standalone revert-test for PayPal webhook header + verification status helpers.
 * Run: php tests/phpunit/extensions/paypal_commerce/WebhookHeadersTest.php
 */

require dirname(__DIR__, 4) . '/public_html/extensions/paypal_commerce/vendor/autoload.php';
require dirname(__DIR__, 4) . '/public_html/extensions/paypal_commerce/core/paypal_commerce_modules.php';

$failed = 0;

function webhook_headers_assert($cond, $msg)
{
    global $failed;
    if ($cond) {
        echo "ok - $msg\n";
        return;
    }
    $failed++;
    echo "not ok - $msg\n";
}

webhook_headers_assert(
    paypalCommerceExtractWebhookHeaders([]) === null,
    'empty SERVER fails closed'
);

webhook_headers_assert(
    paypalCommerceExtractWebhookHeaders([
        'HTTP_PAYPAL_AUTH_ALGO' => 'SHA256withRSA',
        'HTTP_PAYPAL_CERT_URL' => 'https://api.paypal.com/v1/notifications/certs/CERT-1',
        'HTTP_PAYPAL_TRANSMISSION_ID' => 'tid',
        'HTTP_PAYPAL_TRANSMISSION_SIG' => 'sig',
        'HTTP_PAYPAL_TRANSMISSION_TIME' => '',
    ]) === null,
    'blank transmission_time fails closed'
);

$ok = paypalCommerceExtractWebhookHeaders([
    'HTTP_PAYPAL_AUTH_ALGO' => 'SHA256withRSA',
    'HTTP_PAYPAL_CERT_URL' => 'https://api.paypal.com/v1/notifications/certs/CERT-1',
    'HTTP_PAYPAL_TRANSMISSION_ID' => 'tid',
    'HTTP_PAYPAL_TRANSMISSION_SIG' => 'sig',
    'HTTP_PAYPAL_TRANSMISSION_TIME' => '2016-02-18T20:01:35Z',
]);
webhook_headers_assert(
    is_array($ok) && $ok['transmission_id'] === 'tid',
    'complete headers are accepted'
);

webhook_headers_assert(
    paypalCommerceWebhookVerificationIsSuccess('SUCCESS') === true,
    'SUCCESS is accepted'
);
webhook_headers_assert(
    paypalCommerceWebhookVerificationIsSuccess('FAILURE') === false,
    'FAILURE is rejected'
);
webhook_headers_assert(
    paypalCommerceWebhookVerificationIsSuccess('') === false,
    'empty verification status is rejected'
);

if ($failed > 0) {
    fwrite(STDERR, $failed . " assertion(s) failed\n");
    exit(1);
}
echo "all passed\n";
exit(0);
