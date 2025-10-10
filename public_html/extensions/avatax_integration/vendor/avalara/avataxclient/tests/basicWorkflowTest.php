<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers AvaTaxClient
 */
final class basicWorkflowTest extends TestCase
{
    public function testConstructorThrowsExceptionForMissingRequirements()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('appName and appVersion are mandatory fields!');
    
        new Avalara\AvaTaxClient('', '', '', '');
    }
    
    public function testBasicWorkflow()
    {
        // Create a new client
        $client = new Avalara\AvaTaxClient('phpTestApp', '1.0', 'travis-ci', 'sandbox');
        $client->withSecurity(getenv('SANDBOX_USERNAME'), getenv('SANDBOX_PASSWORD'));
        // Call 'Ping' to verify that we are connected
        $p = $client->Ping();
        $this->assertNotNull($p, "Should be able to call Ping");
        $this->assertEquals(true, $p->authenticated, "Environment variables should provide correct authentication");

        // Create a basic company with nexus in the state of Washington
        $model = new Avalara\CompanyInitializationModel();
        $model->city = "Bainbridge Island";
        $model->companyCode = substr(uniqid(), 0, 25);
        $model->country = "US";
        $model->email = "bob@example.org";
        $model->faxNumber = null;
        $model->firstName = "Bob";
        $model->lastName = "McExample";
        $model->line1 = "100 Ravine Lane";
        $model->mobileNumber = "206 555 1212";
        $model->phoneNumber = "206 555 1212";
        $model->postalCode = "98110";
        $model->region = "WA";
        $model->taxpayerIdNumber = "123456789";
        $model->name = "Bob's Greatest Popcorn";
        $model->title = "Owner/CEO";
        $testCompany = $client->companyInitialize($model);
        // Assert that company setup succeeded
        $this->assertNotNull($testCompany, "Test company should be created");
        $this->assertTrue(count($testCompany->nexus) > 0, "Test company should have nexus");
        $this->assertTrue(count($testCompany->locations)> 0, "Test company should have locations");

        // Construct and assert if we can initiate a new transaction builder object
        $tb = new Avalara\TransactionBuilder($client, "DEFAULT", Avalara\DocumentType::C_SALESINVOICE, 'ABC');
        $this->assertNotNull($tb, "TransactionBuilder object can be created");
        $this->assertInstanceOf(Avalara\TransactionBuilder::class, $tb);

        // Put in transaction details to the TransactionBuilder object we just created
        $t = $tb->withAddress('ShipFrom', '123 Main Street', null, null, 'Irvine', 'CA', '92615', 'US')
            ->withAddress('ShipTo', '100 Ravine Lane', null, null, 'Bainbridge Island', 'WA', '98110', 'US')
            ->withLine(100.0, 1, null, "P0000000")
            ->withLine(1234.56, 1, null, "P0000000")
            ->withExemptLine(50.0, null, "NT")
            ->withLine(2000.0, 1, null, "P0000000")
            ->withLineAddress(Avalara\TransactionAddressType::C_SHIPFROM, "123 Main Street", null, null, "Irvine", "CA", "92615", "US")
            ->withLineAddress(Avalara\TransactionAddressType::C_SHIPTO, "1500 Broadway", null, null, "New York", "NY", "10019", "US")
            ->withLine(50.0, 1, null, "FR010000")
            ->create();

        $this->assertNotNull($t, "Response stdClass is not null");

        // echo out the transaction response from CreateTransaction
        // echo '<pre>' . json_encode($t, JSON_PRETTY_PRINT) . '</pre>';
    }

    public function testExtendingAvaTaxWorkFlow()
    {
        // Create an instance of an extended class of AvaTaxClient, and test the inherited functionalities and also the ability to add additional config/info on the client object
        $client = new Avalara\ClientExtensionExample('phpTestApp', '1.0', 'travis-ci', 'sandbox', [], 'my additional client info');
        $myClient = $client->getClient();

        // assertions with comment
        $this->assertInstanceOf(Avalara\AvaTaxClient::class, $myClient, "getClient returns an AvaTaxClient object when called by an instance from an extended class");
        $this->assertTrue($myClient->echoAddedConfig() == "my additional client info", "Extended method can hold additional client configuration/info");
        
        // add credentials
        $client->withSecurity(getenv('SANDBOX_USERNAME'), getenv('SANDBOX_PASSWORD'));

        // Call 'Ping' to verify that we are connected
        $p = $client->Ping();
        $this->assertNotNull($p, "Should be able to call Ping");
    }
}
