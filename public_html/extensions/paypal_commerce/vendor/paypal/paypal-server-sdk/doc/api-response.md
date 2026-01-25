
# ApiResponse

Represents the result of an API call, including the request details, response metadata, and the returned data.

## Methods

| Name | Type | Description |
|  --- | --- | --- |
| `getRequest()` | [`HttpRequest`](../doc/http-request.md) | Returns the original request that resulted in this response. |
| `getStatusCode()` | `?int` | Returns the response status code. |
| `getHeaders()` | `?array` | Returns the response headers. |
| `getResult()` | `mixed` | Returns the response data. |
| `getBody()` | `mixed` | Returns the original body from the response. |
| `isSuccess()` | `bool` | Checks if the response is successful (HTTP 2xx). |
| `isError()` | `bool` | Checks if the response indicates an error. (not HTTP 2xx) |

## Usage Example

```php
$response = $client->exampleController()->exampleEndpoint($input);

if ($response->isSuccess()) {
    echo "Success! Result: ";
    print_r($response->getResult());
} else {
    echo "Error: ";
    print_r($response->getBody());
}
```

