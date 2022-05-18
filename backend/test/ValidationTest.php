<?php

namespace JustCode\JustContactBackend\Test;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\UploadedFile;
use JustCode\JustContactBackend\Backend;
use JustCode\JustContactBackend\Validation;

final class ValidationTest extends TestCase
{
    public function testBasicValidate()
    {
        // given mandatory form data filled out
        $data = array(
            "name" => "Guest",
            "message" => "I like this form so much!"
        );
        // when we validate
        $output = Validation::validate($data);
        // then no validation message appears
        $this->assertTrue(empty($output));
    }

    public function testBasicValidate_missing_name()
    {
        // given not all mandatory form data filled out
        $data = array(
            "message" => "I like this form so much!"
        );
        // when we validate
        $output = Validation::validate($data);
        // then a validation message appears
        $this->assertTrue(!empty($output));
    }

    public function testBasicValidate_missing_message()
    {
        // given not all mandatory form data filled out
        $data = array(
            "name" => "Guest",
        );
        // when we validate
        $output = Validation::validate($data);
        // then a validation message appears
        $this->assertTrue(!empty($output));
    }

    public function testValidateFile_not_allow_txt()
    {
        // given a wrong file format sent
        $file = new UploadedFile(__DIR__ . "/test_file.txt", "test_file.txt", "plain/text", 0);
        $backend = new Backend();
        // when we validate file
        $output = Validation::validateFile($file, $backend->maxFileSize, $backend->mimeTypes);
        // then validation error appears
        $this->assertTrue(!empty($output));
    }

    public function testValidateFile()
    {
        // given valid picture uploaded
        $file = new UploadedFile(__DIR__ . "/code.jpeg", "code.jpeg", "image/jpeg", 0);
        $backend = new Backend();
        // when we validate file
        $output = Validation::validateFile($file, $backend->maxFileSize, $backend->mimeTypes);
        // then no validation message appear
        $this->assertTrue(empty($output));
    }

    public function testValidateFile_too_big()
    {
        // given mandatory form data filled out
        $file = new UploadedFile(__DIR__ . "/code.jpeg", "code2.jpeg", "image/jpeg", 0);
        $backend = new Backend();
        // when we validate file
        $output = Validation::validateFile($file, 2048, $backend->mimeTypes);
        // then a validation message appear
        $this->assertTrue(!empty($output));
    }
}
