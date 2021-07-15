<?php
require_once 'vendor/autoload.php';


print_r(\App\business\Task::getPossibleActions('new', 1, null, 2));

