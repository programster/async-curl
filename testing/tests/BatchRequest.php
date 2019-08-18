<?php

/*
 * Send off a single curl request.
 */

require_once(__DIR__ . '/../../vendor/autoload.php');

function main()
{
    $curlHandler = new Programster\AsyncCurl\BasicRequestHandler();

    for ($i=0; $i<1000; $i++)
    {
        $params = array("request_id" => $i);
        $headers = array("header1" => "value1");

        $request = new \Programster\AsyncCurl\BasicRequest(
            "http://localhost:8081",
            Programster\AsyncCurl\Method::createPost(),
            $timeout=5,
            $params,
            $headers
        );

        $curlHandler->add($request);
    }

    print "finished generating requests. Now going to send them" . PHP_EOL;
    $curlHandler->run();
    $responses = $curlHandler->getResponses();
    print "responses: " . print_r($responses, true) . PHP_EOL;
    print "num responses: " . count($responses) . PHP_EOL;
}

main();