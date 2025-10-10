<?php

// Include the AvaTaxClient library
require_once '/src/AvaTaxClient.php';
use Avalara\AvaTaxClient;

// Create a new client
$client = new Avalara\AvaTaxClient('phpTestApp', '1.0', 'localhost', 'sandbox');

// Call 'Ping' to verify that we are connected
$p = $client->Ping();

?>
<html>
<head>
    <title>AvaTax PHP Example</title>
</head>
<body>
<h1>AvaTax PHP Example</h1>

<p>This example calls the 'Ping' API to detect whether we are connected to AvaTax.

<div style="width:960px;">
    <pre>
        <?php echo json_encode($p, JSON_PRETTY_PRINT) ?>
    </pre>
</div>

</body>
</html>