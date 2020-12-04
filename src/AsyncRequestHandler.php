<?php

/*
 * This class is simply responsible for taking requests and sending them off asynchronously.
 * You can think of these requests as being sent off in parallel though.
 * You may or may not wish to use this with one of the many queues.
 */

namespace Programster\AsyncCurl;


class AsyncRequestHandler
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
     * Add a curl request to this handler.
     * @param \Programster\AsyncCurl\AsyncRequest $request
     * @return int - the ID for the request. This will line up with the ID in the responses array
     * @throws \Exception
     */
    public function add(AsyncRequest $request) : int
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
                    $this->m_curlCode = curl_multi_exec($this->m_curlMultiResource, $this->m_running);

                    while (($info = curl_multi_info_read($this->m_curlMultiResource)) !== false)
                    {
                        $response = new CurlInfoReadResponse($info);

                        if ($response->getResult() === CURLE_OK)
                        {
                            $resourceID = intval($response->getHandle());
                            $request = $this->m_requests[$resourceID];
                            $this->handleCompletedRequest($request);
                        }
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


    private function handleCompletedRequest(AsyncRequest $request)
    {
        $resourceId = intval($request->getCurlResource());
        unset($this->m_requests[$resourceId]);

        $responseContent = curl_multi_getcontent($request->getCurlResource());
        $response = new Response($request, $responseContent);
        $this->m_responses[$resourceId] = $response;

        // run the request response handler.
        $responseHandler = $request->getResponseHandler();
        $responseHandler($response);

        curl_multi_remove_handle($this->m_curlMultiResource, $request->getCurlResource());
    }


    /**
     * Mark this object as completed and clean up.
     */
    private function markCompleted()
    {
        $this->m_state = self::SATE_COMPLETED;

        // close the handles
        foreach ($this->m_requests as $request)
        {
            /* @var $request AsyncRequest */
            $this->handleCompletedRequest($request);
        }

        curl_multi_close($this->m_curlMultiResource);
    }


    # Accessors
    public function getState() { return $this->m_state; }
    public function getResponses() { return $this->m_responses; }
}