<?php
require_once 'vendor/autoload.php';

try {

  $categories = \App\Service\CSVToSQLFileConverter::convert('data\categories.csv', 'data/', 'category');
  $cities = \App\Service\CSVToSQLFileConverter::convert('data/cities.csv', 'data/', 'city');
  $users = \App\Service\CSVToSQLFileConverter::convert('data/users.csv', 'data/', 'user',',', ['last_visit_date'], function() {
      return ['2018-12-21'];
  });
  $profiles = \App\Service\CSVToSQLFileConverter::convert('data/profiles.csv', 'data/', 'profile', ',', ['city_id', 'user_id'],function() {
      return [rand(1,1108),rand(1,20)];
    });
    $tasks = \App\Service\CSVToSQLFileConverter::convert('data/tasks.csv', 'data/', 'task', ',', ['client_id', 'executor_id', 'status', 'city_id'], function() {
        return [
            rand(1,20),
            rand(1,20),
            rand(0,4),
            rand(1, 1108)
        ];
    });

    $respond = \App\Service\CSVToSQLFileConverter::convert('data/replies.csv', 'data/', 'respond', ',', ['user_id', 'task_id', 'status'], function() {
        return [rand(1,20), rand(1,10), 0];
    });

    $recall = \App\Service\CSVToSQLFileConverter::convert('data/opinions.csv', 'data/', 'recall', ',', ['task_id'], function() {
        return [rand(1,10)];
    });


} catch (\App\Exception\SourceFileException $exception) {
    print_r('Ошибка открытия файла: '. $exception->getMessage());
} catch (\App\Exception\FileFormatException $exception) {
    print_r('Ошибка чтения из файла: '. $exception->getMessage());
} catch (\App\Exception\DataException $exception) {
    print_r('Ошибка данных: ' . $exception->getMessage());
}


