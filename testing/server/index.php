<?php

$responseArray = [
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER["CONTENT_TYPE"],
    'headers' => getallheaders(),
];

switch($_SERVER['REQUEST_METHOD'])
{
    case 'POST':
    {
        $responseArray['parameters'] = $_POST;
    }
    break;

    case 'GET':
    {
        $responseArray['parameters'] = $_GET;
    }
    break;

    case 'DELETE':
    {
        parse_str(file_get_contents("php://input"), $variables);
        $responseArray['parameters'] = $variables;
    }
    break;

    case 'PUT':
    {
        parse_str(file_get_contents("php://input"), $variables);
        $responseArray['parameters'] = $variables;
    }
    break;

    case 'PATCH':
    {
        parse_str(file_get_contents("php://input"), $variables);
        $responseArray['parameters'] = $variables;
    }
    break;
}



header('Content-Type: application/json');
usleep(random_int(0, 10000));
$responseCodes = [200, 400, 401, 403, 404, 500];
$responseCodeIndex = random_int(0, count($responseCodes)-1);
$responseCode = $responseCodes[$responseCodeIndex];
http_response_code($responseCode);

die(json_encode($responseArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));