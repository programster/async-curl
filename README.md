# async-curl
A package to make it easier to send curl request asynchronously using [curl_multi_exec](https://secure.php.net/manual/en/function.curl-multi-exec.php).

## Examples

### Basic Batch Request

```php
$curlHandler = new Programster\AsyncCurl\BasicRequestHandler();

// create 1,000 requests to fire off asynchronously
for ($i=0; $i<1000; $i++)
{
    $params = array("request_id" => $i);
    $headers = array("header1" => "value1");

    $request = new \Programster\AsyncCurl\BasicRequest(
        "http://localhost:8081",
        Programster\AsyncCurl\Method::createPost(),
        $timeout=5, // seconds
        $params,
        $headers
    );

    $curlHandler->add($request);
}

// Fire the requests and get the responses
$curlHandler->run();

// get the responses array and do something with them
$responses = $curlHandler->getResponses();

foreach ($responses as $response)
{
    /* @var $response Programster\AsyncCurl\Response */
    // do something
}
```


### Handle Responses Asynchronously As They Come Back

```php
$curlHandler = new Programster\AsyncCurl\AsyncRequestHandler();

// create 1,000 requests
for ($i=0; $i<1000; $i++)
{
    $params = array("request_id" => $i);
    $headers = array("header1" => "value1");

    // create the handler that will handle the response immediately after it comes back.
    $handler = function(Programster\AsyncCurl\Response $response) {
        if ($response->hadCurlError() === false)
        {
            print "Request went through ok: " . PHP_EOL;
            print "response code: " . $response->getHttpCode() . PHP_EOL;
            print "handling response " . $response->getResponseBody() . PHP_EOL;
        }
        else
        {
            print "There was an issue with the request: " . PHP_EOL;
            print "error code: " . $response->getCurlErrorCode() . PHP_EOL;
            print "error message: " . $response->getCurlErrorMessage() . PHP_EOL;
        }
    };

    $request = new \Programster\AsyncCurl\AsyncRequest(
        "http://localhost:8081",
        Programster\AsyncCurl\Method::createPost(),
        $timeout=5,
        $params,
        $headers,
        $handler
    );

    $curlHandler->add($request);
}

// Have the curl handler fire off the requests. Here it is non-blocking so you could do other
// things while the requests are being sent/recieved.
while ($curlHandler->getState() !== Programster\AsyncCurl\AsyncRequestHandler::SATE_COMPLETED)
{
    $curlHandler->run();
    // possibly do other things...?
    usleep(10); // don't waste CPU
}

// After getting out of the while loop, all requests will have had their handlers run against
// their response, but you can still get the responses from the handler should you desire...
$responses = $curlHandler->getResponses();
```