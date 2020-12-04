<?php

/*
 * This class is simply responsible for taking requests and sending them off asynchronously.
 * You can think of these requests as being sent off in parallel though.
 * You may or may not wish to use this with one of the many queues.
 */

namespace Programster\AsyncCurl;

class BasicRequestHandler
{
    const STATE_NOT_YET_STARTED = 0;
    const STATE_RUNNING = 1;
    const SATE_COMPLETED = 2;


    private $m_curlMultiResource;
    private $m_requests;
    private $m_running = false;
    private $m_state;
    private $m_curlCode;
    private $m_responses;


    public function __construct()
    {
        $this->m_running = false;
        $this->m_state = self::STATE_NOT_YET_STARTED;
        $this->m_curlMultiResource = curl_multi_init();
    }


    /**
     * Add a curl request to this object.
     * @param \Programster\AsyncCurl\BasicRequest $request
     * @return int - the ID for the request. This will line up with the ID in the responses array
     * @throws \Exception
     */
    public function add(BasicRequest $request) : int
    {
        $resourceId = intval($request->getCurlResource());

        if ($this->m_state === self::STATE_NOT_YET_STARTED)
        {
            $this->m_requests[$resourceId] = $request;
            curl_multi_add_handle($this->m_curlMultiResource, $request->getCurlResource());
        }
        else
        {
            throw new \Exception("You cannot add items to a curl handler once it has started.");
        }

        return $resourceId;
    }


    /**
     * Run the list of requests as shown here:
     * http://www.adrianworlddesign.com/Knowledge-Base/php/Download-content-with-cURL/Doing-curlmultiexec-the-right-way
     * These references are also useful:
     * https://secure.php.net/manual/en/function.curl-multi-exec.php
     * https://stackoverflow.com/questions/15559157/understanding-php-curl-multi-exec
     */
    public function run()
    {
        $this->m_state = self::STATE_RUNNING;
        $active = null;

        do {
            curl_multi_exec($this->m_curlMultiResource, $active);
        } while ($active);

        $this->markCompleted();
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
            /* @var $request BasicRequest */
            $request = array_pop($this->m_requests);
            $responseContent = curl_multi_getcontent($request->getCurlResource());
            $response = new Response($request, $responseContent);
            $this->m_responses[] = $response;
            /* @var $request CurlRequestInterface */
            curl_multi_remove_handle($this->m_curlMultiResource, $request->getCurlResource());
        }

        curl_multi_close($this->m_curlMultiResource);
    }


    # Accessors
    public function getState() { return $this->m_state; }
    public function getResponses() { return $this->m_responses; }
}