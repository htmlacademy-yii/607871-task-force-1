<?php
require_once 'vendor/autoload.php';

try {
    print_r(\App\business\Task::getStatusByAction('done'));
} catch (\App\Exception\DataException $e) {
    print_r('Ошибка: '. $e->getMessage());
}


