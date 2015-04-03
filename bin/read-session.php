<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$filename = $argv[1];

if (!$filename) {
    echo('No session filename given' . "\n");
    exit(-1);
}

if (!is_file($filename) || !is_readable($filename)) {
    echo('Session file "' . $filename . '" not found or not readable' . "\n");
    exit(-1);
}

$sessionData = file_get_contents($filename);

$sessionReader = new \KristsK\PhpIgbinary\Reader($sessionData);

$sessionPrinter = new \KristsK\PhpIgbinary\Reader\Printer(
    $sessionReader,
    function ($m) {

        echo($m);
    },
    function () {

        echo("\n");
    }
);

\Psy\Shell::debug([
    'sessionReader' => $sessionReader,
    'sessionPrinter' => $sessionPrinter
]);
