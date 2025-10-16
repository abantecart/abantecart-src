<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers AvaTaxClient
 */
final class RegisterShipmentTest extends TestCase
{
    
    public function testRegisterShipment()
    {
        // Create a new client
        $client = new Avalara\AvaTaxClient('phpTestApp', '1.0', 'travis-ci', 'sandbox');
        $client->withSecurity(getenv('SANDBOX_USERNAME'), getenv('SANDBOX_PASSWORD'));
        // Call 'Ping' to verify that we are connected
        $p = $client->registerShipment("DEFAULT", "063e1af4-11d3-4489-b8ba-ae1149758df4", null);
        print_r($p);
        $this->assertNotNull($p);
    }

}