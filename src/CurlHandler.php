<?php

/* 
 * This class is simply responsible for taking requests and sending them off asynchronously.
 * You can think of these requests as being sent off in parallel though.
 * You may or may not wish to use this with one of the many queues.
 */

class CurlHandler
{
    const STATE_NOT_YET_STARTED = 0;
    const STATE_RUNNING = 1;
    const SATE_COMPLETED = 2;
    
    
    private $m_curlMultiResource;
    private $m_requests;
    private $m_running = false;
    private $m_state = self::STATE_NOT_YET_STARTED;
    private $m_curlCode;
    
    public function __construct()
    {
        $this->m_curlMultiResource = curl_multi_init();
        $this->m_state = self::STATE_NOT_YET_STARTED;
    }
    
    public function __destruct() 
    {
        curl_multi_close($this->m_curlMultiResource);
    }
    
    
    /**
     * Add a curl request to this object.
     * @param CurlRequestInterface $request
     * @throws Exception
     */
    public function add(CurlRequestInterface $request)
    {
        if ($this->m_state == self::STATE_NOT_YET_STARTED)
        {
            $this->m_requests[] = $request;
            curl_multi_add_handle($this->m_curlMultiResource, $request->getCurlResource());
        }
        else
        {
            throw new Exception("You cannot add items to a curl handler once it has stareted.");
        }
    }
    
    
    /**
     * Run the list of requests as shown here:
     * https://secure.php.net/manual/en/function.curl-multi-exec.php
     * 
     * These references are also useful:
     * https://stackoverflow.com/questions/15559157/understanding-php-curl-multi-exec
     * http://www.adrianworlddesign.com/Knowledge-Base/php/Download-content-with-cURL/Doing-curlmultiexec-the-right-way
     */
    public function run()
    {
        switch ($this->m_state)
        {
            case self::STATE_NOT_YET_STARTED:
            {
                do 
                {
                    $this->m_curlCode = curl_multi_exec($this->m_curlMultiResource, $this->m_running);
                    $this->m_state = self::STATE_RUNNING; 
                } while ($this->m_curlCode == CURLM_CALL_MULTI_PERFORM);
            }
            break;
            
            case self::STATE_RUNNING:
            {
                if ($this->m_running && $this->m_curlCode == CURLM_OK) 
                {
                    // set timeout to 0 so we dont block
                    // will return 0 if there was no activity or -1 if there was an error.
                    // https://secure.php.net/manual/en/function.curl-multi-select.php
                    if (curl_multi_select($this->m_curlMultiResource, $timeout=0) > 0) 
                    {
                        do 
                        {
                            $this->m_curlCode = curl_multi_exec($this->m_curlMultiResource, $this->m_running);
                        } while ($this->m_curlCode == CURLM_CALL_MULTI_PERFORM);
                    }
                }
                else
                {
                    $this->markCompleted();
                }
                
                if ($this->m_running == 0)
                {
                    $this->markCompleted();
                }
            }
            break;
        
            case self::SATE_COMPLETED:
            {
                // do nothing. There is no point being here.
            }
            break;
        }
    }
    
    
    /**
     * Mark this object as completed and clean up.
     */
    private function markCompleted()
    {
        $this->m_state = self::SATE_COMPLETED;
        
        // close the handles
        while (count($this->m_requests) > 0)
        {
            /* @var $curlRequest CurlRequestInterface */
            $curlRequest = array_pop($this->m_requests);
            
            /* @var $request CurlRequestInterface */
            curl_multi_remove_handle($this->m_curlMultiResource, $curlRequest->getCurlResource());
        }
        
        curl_multi_close($this->m_curlMultiResource);
    }
    
    
    # Accessors
    public function getState() { return $this->m_state; }
}