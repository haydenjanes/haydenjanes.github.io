<?php

namespace JustCode\JustContactBackend;

/**
 * Verify the response token from the client
 */
class ReCaptcha
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Send a POST request to Google service to verify the token
     */
    public function verify($token)
    {
        $fieldsString = '';
        // TODO: The private key need to be change to production key later
        $fields = array(
            'secret' => $this->secret,
            'response' => $token
        );
        foreach ($fields as $key => $value) {
            $fieldsString .= $key . '=' . $value . '&';
        }

        $fieldsString = rtrim($fieldsString, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
