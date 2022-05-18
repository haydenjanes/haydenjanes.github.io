<?php
// Main entry point to the simple backend.

namespace JustCode\JustContactBackend;

require dirname(__FILE__) . '/vendor/autoload.php';

$backend = new Backend();
$backend->process();
