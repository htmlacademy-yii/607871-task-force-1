<?php
require_once 'vendor/autoload.php';

try {
    print_r(\App\business\Task::getStatusByAction('done'));
    print_r(\App\business\Task::getPossibleActions('new1', 1, null, 3));
} catch (\App\Exception\DataException $e) {
    print_r('Ошибка: '. $e->getMessage());
}


