<?php

/*
 * A wrapper around curl_multi_info_read to make life easier:
 * https://www.php.net/manual/en/function.curl-multi-info-read.php
 */

namespace Programster\AsyncCurl;

class CurlInfoReadResponse
{
    private $m_handle;
    private $m_result;


    public function __construct(array $info)
    {
        $requiredKeys = ['msg', 'handle', 'result'];
        $missingKeys = array_diff($requiredKeys, array_keys($info));

        if (count($missingKeys) > 0)
        {
            throw new \Exception("Trying to create a CurlInfoReadResponse from a non non curl_multi_info_read response");
        }

        $this->m_result = $info['result'];
        $this->m_handle = $info['handle'];
    }


    # Accessors
    public function getHandle() { return $this->m_handle; }
    public function getResult() : int { return $this->m_result; }

    /**
     * Get the string name for the result integer. This aids with understanding.
     * @return string
     */
    public function getResultName() : string
    {
        switch ($this->getResult())
        {
            case CURLE_OK:                          $resultName = "CURLE_OK";                          break;
            case CURLE_UNSUPPORTED_PROTOCOL:        $resultName = "CURLE_UNSUPPORTED_PROTOCOL";        break;
            case CURLE_FAILED_INIT:                 $resultName = "CURLE_FAILED_INIT";                 break;
            case CURLE_URL_MALFORMAT:               $resultName = "CURLE_URL_MALFORMAT";               break;
            case CURLE_URL_MALFORMAT_USER:          $resultName = "CURLE_URL_MALFORMAT_USER";          break;
            case CURLE_COULDNT_RESOLVE_PROXY:       $resultName = "CURLE_COULDNT_RESOLVE_PROXY";       break;
            case CURLE_COULDNT_RESOLVE_HOST:        $resultName = "CURLE_COULDNT_RESOLVE_HOST";        break;
            case CURLE_COULDNT_CONNECT:             $resultName = "CURLE_COULDNT_CONNECT";             break;
            case CURLE_FTP_WEIRD_SERVER_REPLY:      $resultName = "CURLE_FTP_WEIRD_SERVER_REPLY";      break;
            case CURLE_FTP_ACCESS_DENIED:           $resultName = "CURLE_FTP_ACCESS_DENIED";           break;
            case CURLE_FTP_USER_PASSWORD_INCORRECT: $resultName = "CURLE_FTP_USER_PASSWORD_INCORRECT"; break;
            case CURLE_FTP_WEIRD_PASS_REPLY:        $resultName = "CURLE_FTP_WEIRD_PASS_REPLY";        break;
            case CURLE_FTP_WEIRD_USER_REPLY:        $resultName = "CURLE_FTP_WEIRD_USER_REPLY";        break;
            case CURLE_FTP_WEIRD_PASV_REPLY:        $resultName = "CURLE_FTP_WEIRD_PASV_REPLY";        break;
            case CURLE_FTP_WEIRD_227_FORMAT:        $resultName = "CURLE_FTP_WEIRD_227_FORMAT";        break;
            case CURLE_FTP_CANT_GET_HOST:           $resultName = "CURLE_FTP_CANT_GET_HOST";           break;
            case CURLE_FTP_CANT_RECONNECT:          $resultName = "CURLE_FTP_CANT_RECONNECT";          break;
            case CURLE_FTP_COULDNT_SET_BINARY:      $resultName = "CURLE_FTP_COULDNT_SET_BINARY";      break;
            case CURLE_PARTIAL_FILE:                $resultName = "CURLE_PARTIAL_FILE";                break;
            case CURLE_FTP_COULDNT_RETR_FILE:       $resultName = "CURLE_FTP_COULDNT_RETR_FILE";       break;
            case CURLE_FTP_WRITE_ERROR:             $resultName = "CURLE_FTP_WRITE_ERROR";             break;
            case CURLE_FTP_QUOTE_ERROR:             $resultName = "CURLE_FTP_QUOTE_ERROR";             break;
            case CURLE_HTTP_NOT_FOUND:              $resultName = "CURLE_HTTP_NOT_FOUND";              break;
            case CURLE_WRITE_ERROR:                 $resultName = "CURLE_WRITE_ERROR";                 break;
            case CURLE_MALFORMAT_USER:              $resultName = "CURLE_MALFORMAT_USER";              break;
            case CURLE_FTP_COULDNT_STOR_FILE:       $resultName = "CURLE_FTP_COULDNT_STOR_FILE";       break;
            case CURLE_READ_ERROR:                  $resultName = "CURLE_READ_ERROR";                  break;
            case CURLE_OUT_OF_MEMORY:               $resultName = "CURLE_OUT_OF_MEMORY";               break;
            case CURLE_OPERATION_TIMEOUTED:         $resultName = "CURLE_OPERATION_TIMEOUTED";         break;
            case CURLE_FTP_COULDNT_SET_ASCII:       $resultName = "CURLE_FTP_COULDNT_SET_ASCII";       break;
            case CURLE_FTP_PORT_FAILED:             $resultName = "CURLE_FTP_PORT_FAILED";             break;
            case CURLE_FTP_COULDNT_USE_REST:        $resultName = "CURLE_FTP_COULDNT_USE_REST";        break;
            case CURLE_FTP_COULDNT_GET_SIZE:        $resultName = "CURLE_FTP_COULDNT_GET_SIZE";        break;
            case CURLE_HTTP_RANGE_ERROR:            $resultName = "CURLE_HTTP_RANGE_ERROR";            break;
            case CURLE_HTTP_POST_ERROR:             $resultName = "CURLE_HTTP_POST_ERROR";             break;
            case CURLE_SSL_CONNECT_ERROR:           $resultName = "CURLE_SSL_CONNECT_ERROR";           break;
            case CURLE_FTP_BAD_DOWNLOAD_RESUME:     $resultName = "CURLE_FTP_BAD_DOWNLOAD_RESUME";     break;
            case CURLE_FILE_COULDNT_READ_FILE:      $resultName = "CURLE_FILE_COULDNT_READ_FILE";      break;
            case CURLE_LDAP_CANNOT_BIND:            $resultName = "CURLE_LDAP_CANNOT_BIND";            break;
            case CURLE_LDAP_SEARCH_FAILED:          $resultName = "CURLE_LDAP_SEARCH_FAILED";          break;
            case CURLE_LIBRARY_NOT_FOUND:           $resultName = "CURLE_LIBRARY_NOT_FOUND";           break;
            case CURLE_FUNCTION_NOT_FOUND:          $resultName = "CURLE_FUNCTION_NOT_FOUND";          break;
            case CURLE_ABORTED_BY_CALLBACK:         $resultName = "CURLE_ABORTED_BY_CALLBACK";         break;
            case CURLE_BAD_FUNCTION_ARGUMENT:       $resultName = "CURLE_BAD_FUNCTION_ARGUMENT";       break;
            case CURLE_BAD_CALLING_ORDER:           $resultName = "CURLE_BAD_CALLING_ORDER";           break;
            case CURLE_HTTP_PORT_FAILED:            $resultName = "CURLE_HTTP_PORT_FAILED";            break;
            case CURLE_BAD_PASSWORD_ENTERED:        $resultName = "CURLE_BAD_PASSWORD_ENTERED";        break;
            case CURLE_TOO_MANY_REDIRECTS:          $resultName = "CURLE_TOO_MANY_REDIRECTS";          break;
            case CURLE_UNKNOWN_TELNET_OPTION:       $resultName = "CURLE_UNKNOWN_TELNET_OPTION";       break;
            case CURLE_TELNET_OPTION_SYNTAX:        $resultName = "CURLE_TELNET_OPTION_SYNTAX";        break;
            case CURLE_OBSOLETE:                    $resultName = "CURLE_OBSOLETE";                    break;
            case CURLE_SSL_PEER_CERTIFICATE:        $resultName = "CURLE_SSL_PEER_CERTIFICATE";        break;
            case CURLE_GOT_NOTHING:                 $resultName = "CURLE_GOT_NOTHING";                 break;
            case CURLE_SSL_ENGINE_NOTFOUND:         $resultName = "CURLE_SSL_ENGINE_NOTFOUND";         break;
            case CURLE_SSL_ENGINE_SETFAILED:        $resultName = "CURLE_SSL_ENGINE_SETFAILED";        break;
            case CURLE_SEND_ERROR:                  $resultName = "CURLE_SEND_ERROR";                  break;
            case CURLE_RECV_ERROR:                  $resultName = "CURLE_RECV_ERROR";                  break;
            case CURLE_SHARE_IN_USE:                $resultName = "CURLE_SHARE_IN_USE";                break;
            case CURLE_SSL_CERTPROBLEM:             $resultName = "CURLE_SSL_CERTPROBLEM";             break;
            case CURLE_SSL_CIPHER:                  $resultName = "CURLE_SSL_CIPHER";                  break;
            case CURLE_SSL_CACERT:                  $resultName = "CURLE_SSL_CACERT";                  break;
            case CURLE_BAD_CONTENT_ENCODING:        $resultName = "CURLE_BAD_CONTENT_ENCODING";        break;
            case CURLE_LDAP_INVALID_URL:            $resultName = "CURLE_LDAP_INVALID_URL";            break;
            case CURLE_FILESIZE_EXCEEDED:           $resultName = "CURLE_FILESIZE_EXCEEDED";           break;
            case CURLE_FTP_SSL_FAILED:              $resultName = "CURLE_FTP_SSL_FAILED";              break;
            case CURLE_SSH:                         $resultName = "CURLE_SSH";                         break;
        }
    }
}

