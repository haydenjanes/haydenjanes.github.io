<?php

namespace JustCode\JustContactBackend;

/**
 * Response Handler for HTTP connection.
 */
class Response
{

    /**
     * Format json response to send back and exit script.
     */
    static function send($status, $status_message, $data = null)
    {
        header("HTTP/1.1 " . $status);

        $response['status'] = $status;
        $response['status_message'] = $status_message;
        if ($data != null) {
            $response['data'] = $data;
        }
        $json_response = json_encode($response);
        echo $json_response;
        exit(0);
    }
}
