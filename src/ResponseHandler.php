<?php

interface ResponseHandlerInterface
{
    public function handleResponse(Programster\AsyncCurl\Response $response);
}
