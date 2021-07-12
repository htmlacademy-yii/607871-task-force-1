<?php
require_once 'vendor/autoload.php';


print_r(\App\business\Task::getPossibleActions('in progress', 1, 2, 2));

