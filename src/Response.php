<?php

namespace Programster\AsyncCurl;

class Response
{
    private $m_request;
    private $m_responseBody;
    private $m_responseHeader;
    private $m_rawCurlResponse;
    private $m_requestInfoArray;
    

    public function __construct(CurlRequestInterface $request, string $rawCurlResponse)
    {
        $this->m_request = $request;
        $this->m_rawCurlResponse = $rawCurlResponse;
        $curlResource = $request->getCurlResource();
        $this->m_requestInfoArray = curl_getinfo($curlResource);
        $headerSize = $this->m_requestInfoArray['header_size'];
        $this->m_responseHeader = substr($rawCurlResponse, 0, $headerSize);
        $this->m_responseBody = substr($rawCurlResponse, $headerSize);
    }


    /**
     * Tells you if there was a CURL error.
     * This does not tell you whether the server responded with HTTP status code 200,
     * you should still check that.
     * @return bool - true if there was a curl issue (such as could not connect). False if not.
     */
    public function hadCurlError() : bool
    {
        return !($this->getCurlErrorCode() === 0 && $this->getCurlErrorMessage() === "");
    }


    # Accessors
    public function getCurlErrorCode() : int { return curl_errno($this->m_request->getCurlResource()); }
    public function getCurlErrorMessage() : string { return curl_error($this->m_request->getCurlResource()); }
    public function getRequest() : Programster\AsyncCurl\CurlRequestInterface { return $this->m_request; }
    public function getRawCurlResponse() : string { return $this->m_rawCurlResponse; }
    public function getResponseHeader() : string { return $this->m_responseHeader; }
    public function getResponseBody() : string { return $this->m_responseBody; }
    public function getUrl() : string { return $this->m_requestInfoArray['url']; }
    public function getContentType() { return $this->m_requestInfoArray['content_type']; }
    public function getHttpCode() : int { return $this->m_requestInfoArray['http_code']; }
    public function getHeaderSize() : int { return $this->m_requestInfoArray['header_size']; }
    public function getRequestSize() : int { return $this->m_requestInfoArray['request_size']; }
    public function getFileTime() { return $this->m_requestInfoArray['filetime']; }
    public function getSslVerifyResult() { return $this->m_requestInfoArray['ssl_verify_result']; }
    public function getRedirectCount() { return $this->m_requestInfoArray['redirect_count']; }
    public function getTotalTime() { return $this->m_requestInfoArray['total_time']; }
    public function getNamelookupTime() { return $this->m_requestInfoArray['namelookup_time']; }
    public function getConnectTime() { return $this->m_requestInfoArray['connect_time']; }
    public function getPretransferTime() { return $this->m_requestInfoArray['pretransfer_time']; }
    public function getSizeUpload() { return $this->m_requestInfoArray['size_upload']; }
    public function getSizeDownload() { return $this->m_requestInfoArray['size_download']; }
    public function getSpeedDownload() { return $this->m_requestInfoArray['speed_download']; }
    public function getSpeedUpload() { return $this->m_requestInfoArray['speed_upload']; }
    public function getDownloadContentLength() { return $this->m_requestInfoArray['download_content_length']; }
    public function getUploadContentLength() { return $this->m_requestInfoArray['upload_content_length']; }
    public function getStartTransferTime() { return $this->m_requestInfoArray['starttransfer_time']; }
    public function getRedirectTime() { return $this->m_requestInfoArray['redirect_time']; }
    public function getCertInfo() { return $this->m_requestInfoArray['certinfo']; }
    public function getPrimaryIp() { return $this->m_requestInfoArray['primary_ip']; }
    public function getPrimaryPort() { return $this->m_requestInfoArray['primary_port']; }
    public function getLocalIp() { return $this->m_requestInfoArray['local_ip']; }
    public function getLocalPort() { return $this->m_requestInfoArray['local_port']; }
    public function getRedirectUrl() { return $this->m_requestInfoArray['redirect_url']; }
    public function getRequestHeader() { return $this->m_requestInfoArray['request_header']; }

}

