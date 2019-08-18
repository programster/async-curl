<?php

/*
 * Send off a single curl request.
 */

require_once(__DIR__ . '/../../vendor/autoload.php');

function main()
{
    $curlHandler = new Programster\AsyncCurl\AsyncRequestHandler();

    for ($i=0; $i<1000; $i++)
    {
        $params = array("request_id" => $i);
        $headers = array("header1" => "value1");

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

    print "finished generating requests. Now going to send them" . PHP_EOL;
    while ($curlHandler->getState() !== Programster\AsyncCurl\AsyncRequestHandler::SATE_COMPLETED)
    {
        $curlHandler->run();
        usleep(10);
    }

    die ("num responses: " . count( $curlHandler->getResponses()));
    $output = $curlHandler->getResponses();
}

main();