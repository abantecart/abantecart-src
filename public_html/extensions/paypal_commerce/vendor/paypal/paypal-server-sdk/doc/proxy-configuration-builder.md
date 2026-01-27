
# ProxyConfigurationBuilder

Represents the proxy server configurations for API calls. Create instance using `ProxyConfigurationBuilder::init('http://your.proxy.host')`

## Methods

| Name | Description |
|  --- | --- |
| `address(string $address)` | Sets the proxy host address. Should be set during initialization via init() method. |
| `port(int $port)` | Sets the port used to connect to the proxy server. **Default port:** 0 |
| `tunnel(bool $tunnel)` | Enables or disables tunneling through the proxy server. **Default tunnel:** false |
| `auth(string $user , string $pass)` | Sets both username and password in a single method. **Default user:** '', **Default pass:** '' |
| `authMethod(int $authMethod)` | Sets the proxy authentication method. **Default authMethod:** CURLAUTH_BASIC |

### Client Initialization with Proxy Configuration

To configure the SDK to use a proxy server, initialize the proxy configuration during client setup as shown in the Usage Example.

## Usage Example

```php
<?php

use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Proxy\ProxyConfigurationBuilder;
// initialize the sdk client using a proxy configuration
$client = PaypalServerSdkClientBuilder::init()
    ->proxyConfiguration(
        ProxyConfigurationBuilder::init('http://localhost')
            ->port(8080)
            ->auth('username', 'password')
            ->authMethod(CURLAUTH_BASIC)
            ->tunnel(true)
    )
    ->build();
```

