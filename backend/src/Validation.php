<?php

namespace JustCode\JustContactBackend;

/**
 * Perform server side data validation.
 */
class Validation
{
    /**
     * Perform a basic data validation
     */
    public static function validate($data)
    {
        $error = "";
        if (empty($data["name"])) {
            $error .= "Missing parameter name. ";
        }
        if (empty($data["message"])) {
            $error .= "Missing parameter message. ";
        }
        return $error;
    }

    /**
     * Validate file based on system, limit and mime types.
     */
    public static function validateFile($file, $limit, $mimes)
    {
        // before going to detail checks, first check if file has error:
        if ($file->getError() > 0) {
            return "Uploaded files have errors.";
        }
        $error = "";
        if ($file->getSize() > $limit) {
            $filename = $file->getClientOriginalName();
            $error .= "File $filename is bigger than ${limit} bytes.";
        }
        $found_mime = false;
        $fileMimeType = $file->getClientMimeType();
        foreach ($mimes as $mime) {
            if ($mime == $fileMimeType) {
                $found_mime = true;
                break;
            }
        }
        if (!$found_mime) {
            $error .= " Mimetype $fileMimeType is not supported.";
        }
        return $error;
    }
}
