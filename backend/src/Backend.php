<?php

namespace JustCode\JustContactBackend;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Main backend class accepting REST calls that POST json.
 */
class Backend
{

    private $sharedSecret;
    private $refererFilter;
    private $sleepSeconds;
    private $notAllowed;
    private $xmlHttp;
    private $sender;
    private $recaptcha;
    public $mimeTypes;
    public $maxFileSize;

    public function __construct()
    {
        // read http configuration, refer to http.ini for explanation
        $httpOptions = parse_ini_file(dirname(__FILE__) . "/http.ini");
        $this->sharedSecret = $httpOptions["shared_secret"];
        $this->refererFilter = $httpOptions["referer_filter"];
        $this->notAllowed = $httpOptions["not_allowed_msg"];
        $this->xmlHttp = $httpOptions["xml_http"];
        $this->sleepSeconds = $httpOptions["sleep_seconds"];
        $mimeData = $httpOptions["allowed_mime"];
        $this->mimeTypes = array_map('trim', explode(',',  $mimeData));
        $this->maxFileSize = $httpOptions["max_file_size"];
        $this->recaptcha = new ReCaptcha($httpOptions["recaptcha_secret"]);
        // initialize MailSender
        $this->sender = new MailSender();
    }

    public function process()
    {
        // slow down response in case of misuse
        // assumably, we cannot completely block of unauthorized usage
        sleep($this->sleepSeconds);
        // allow only certain referer,  we check here for a base url
        $ref = $_SERVER['HTTP_REFERER'];
        // strpos should return beginning of $ref
        if (strpos($ref, $this->refererFilter) != 0) {
            Response::send(401, $this->notAllowed);
        }

        // read body form POST request
        $postBody = $_POST;
        if (empty($postBody)) {
            # this is a CORS pre flight request
            # https://developer.mozilla.org/de/docs/Web/HTTP/CORS
            Response::send(200, "OK");
        }
        // valid CORS requests should have this header
        if ($this->xmlHttp != $_SERVER["HTTP_X_REQUESTED_WITH"]) {
            Response::send(401, $this->notAllowed);
        }
        // parse POST body
        $jsonData = json_decode($postBody["meta"], true);
        // if shared secret wrong sent 401
        if ($this->sharedSecret != $jsonData["shared_secret"]) {
            Response::send(401, $this->notAllowed);
        }
        // basic server side field validation
        $error = Validation::validate($jsonData);
        if (!empty($error)) {
            Response::send(400, "validation problem", $error);
        }
        // verify recaptcha
        $recaptchaToken = $jsonData['g-recaptcha-response'];
        $httpResponse = $this->recaptcha->verify($recaptchaToken);
        if (!$httpResponse['success']) {
            Response::send(400, "Please go back and make sure you check the security CAPTCHA box.");
        }
        $uploadedFiles = array();
        if (!empty($_FILES)) {
            // validate send files

            foreach ($_FILES as $file_meta) {
                $file = new UploadedFile($file_meta["tmp_name"], $file_meta["name"], $file_meta["type"], $file_meta["error"]);
                $error = Validation::validateFile($file, $this->maxFileSize, $this->mimeTypes);
                if (!empty($error)) {
                    Response::send(400, "validation problem", $error);
                }
                array_push($uploadedFiles, $file);
            }
        }

        // process form and send response
        $sender = $this->sender;
        if ($sender->sendMessage($jsonData, $uploadedFiles)) {
            Response::send(200, "Message sent.");
        } else {
            Response::send(500, "Mail service not available");
        }
    }
}
