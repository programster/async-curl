<?php

/* 
 * A basic curl request. Make the request and wait for a response.
 */

class BasicRequest implements CurlRequestInterface
{
    protected $m_url;
    protected $m_method;
    protected $m_params;
    protected $m_curlResource;
    
    
    /**
     * Create a basic curl request.
     * @param string $url - the url to send the request to. This should contain http or https at 
     *                      the start and should not contain any parameters, instead providing them
     *                      with the functions params variable.
     * @param Method $method - one of the Method objects representing GET, POST, PUT, DELETE etc.
     * @param int $timeout - the timout in seconds to wait.
     * @param array $params - array of name values to send with the request.
     * @param array $headers - name/value pairs of additional headers you wish to send.
     */
    public function __construct($url, Method $method, $timeout, Array $params, Array $headers)
    {
        $this->m_url = $url;
        $this->m_method = $method;
        $this->m_params = $params;
        $this->m_curlResource = curl_init();
        
        curl_setopt($this->m_curlResource, CURLOPT_HEADER, true);
        curl_setopt($this->m_curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->m_curlResource, CURLOPT_TIMEOUT, $timeout);
        
        $headersArray = array();
        
        foreach ($this->m_headers as $headerName => $headerValue)
        {
            $headersArray[] = $headerName . ': ' . $headerValue;
        }
        
        curl_setopt($this->m_curlResource, CURLOPT_HTTPHEADER, $headersArray);
        
        switch ($this->m_method)
        {
            case 'get':
            {
                $this->m_url .= '?' . http_build_query($this->m_params);
            }
            break;
        
            case 'put':
            {
                curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($this->m_params));
            }
            break;
        
            case 'post':
            {
                curl_setopt($this->m_ch, CURLOPT_POST, true);
                curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $this->m_params);
            }
            break;
        
            case 'patch':
            {
                curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($this->m_params));
            }
            break;
        
            case 'delete':
            {
                curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($this->m_params));
            }
            break;
        }
    }
    
    
    /**
     * Closes cURL resource and frees the memory.
     * It is neccessary when you make a lot of requests
     * and you want to avoid fill up the memory.
     */
    public function __destruct()
    {
        if (isset($this->m_curlResource)) 
        {
            curl_close($this->m_curlResource);
        }
    }
    
    
    public function getCurlResource() 
    {
        return $this->m_curlResource;
    }
}

