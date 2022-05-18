<?php

namespace JustCode\JustContactBackend;

//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Wrap PHPMailer in case other handlers will be used. 
 */
class MailSender implements Sender
{

    private $mailer;

    /**
     * Read all the mail properties and construct PHPMailer
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $mail = $this->mailer;
        // read mail configuration
        $smtp_options = parse_ini_file(dirname(__FILE__) . "/mail.ini");
        // check if internal agent should be used
        if ($smtp_options["smtp_agent"] == "sendmail") {

            $mail->isSendmail();
        } else if ($smtp_options["smtp_agent"] == "SMTP") {
            $mail->isSMTP();
            $mail->Host = $smtp_options['smtp_host'];
            $mail->Port = $smtp_options['smtp_port'];
            $mail->Username = $smtp_options['smtp_username'];
            $mail->Password = $smtp_options['smtp_password'];
            $mail->SMTPAuth = true;
        } else {
            $mail->isMail();
        }
        $mail->setFrom($smtp_options["smtp_from"]);
        $mail->addAddress($smtp_options["mail_to"]);
        $mail->Subject = $smtp_options["mail_subject"];
    }

    /**
     *  Send out the mail using PHPMailer.
     */
    public function sendMessage($jsonData, $files)
    {
        try {
            $mail = $this->mailer;
            $mail->Body = self::generateMessage($jsonData);
            foreach ($files as $file) {
                $mail->AddAttachment(
                    $file->getPathname(),
                    $file->getClientOriginalName(),
                    'base64',
                    $file->getClientMimeType(),
                );
            }
            return $mail->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    // Construct the response message
    private static function generateMessage($json_data)
    {
        $message = "A new message from " . $json_data["name"] . "\n";
        $message .= "Email: " . $json_data["email"] . "\n";
        $message .= "Phone: " . $json_data["phone"] . "\n\n";
        $message .= "" . $json_data["message"] . "\n";
        return $message;
    }
}
