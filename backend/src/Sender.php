<?php

namespace JustCode\JustContactBackend;

/**
 * The Sender uses a channel such as mail, slack or telegram to deliver message.
 */
interface Sender
{
    /**
     * Sends the message based on received jsonData.
     */
    public function sendMessage($jsonData, $files);
}
