<?php

/* 
 * This type of request is sent out and we never wait for a response. There is no guarantee that
 * the requests was even recieved. This is probably good for things like logging trivial events
 */

class FireAndForgetRequest extends BasicRequest
{
    public function __construct($url, Method $method, $timeout, Array $params, Array $headers)
    {
        parent::__construct($url, $method, $timeout, $params, $headers);
        curl_setopt($this->m_curlResource, CURLOPT_RETURNTRANSFER, false);
    }
}

