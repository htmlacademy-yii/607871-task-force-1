<?php
require_once 'vendor/autoload.php';

try {
    $contacts = new \App\Service\DataImporter('data\categories.csv', ['name', 'icon']);
    $contacts->import();
    print_r($contacts->getData());
} catch (\App\Exception\SourceFileException $exception) {
    print_r('Ошибка: '. $exception->getMessage());
} catch (\App\Exception\FileFormatException $exception) {
    print_r('Ошибка: '. $exception->getMessage());
}


