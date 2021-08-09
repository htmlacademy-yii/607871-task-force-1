<?php


namespace App\Service;


use App\Exception\DataException;
use App\Exception\SourceFileException;

class CSVToSQLFileConverter
{

    public static function convert(string $fileName, string $dirName, string $tableName, string $delimiter = ',', array $extraColumns = [], $callback = null)
    {
        $fileObject = BaseFileInspector::checkFileAvailability($fileName, 'csv');
        $fileObject->rewind();
        $csvHeaders = $fileObject->fgetcsv($delimiter);

        $sqlFile = $fileObject->getBasename('.csv') . '.sql';

        $values = [];

        while (!$fileObject->eof()) {
            $csvLine = $fileObject->fgetcsv($delimiter);
            if (!array_filter($csvLine)) {
                continue;
            }

            if ($callback && !is_callable($callback)) {
                throw new DataException("Дополнительные данные не могут быть добавлены в файл {$sqlFile}, проверьте callback параметр. ");
            }

            $valuesLine = ($callback  && is_callable($callback)) ? array_merge($csvLine, call_user_func($callback)): $csvLine;

                $values[] = sprintf("\t(%s)", implode(', ', array_map(function ($value) {
                        return "'$value'";
                    }, $valuesLine)));

        }
        $sqlPath = $dirName . $sqlFile;
        $sqlValues = implode(", \n", $values);
        $sqlColumns = implode(', ', array_map(function ($value) {
            return "`$value`";
        },array_merge($csvHeaders, $extraColumns)));

        $sqlResult = sprintf("INSERT INTO `%s` \n\t(%s) \nVALUES \n%s;", $tableName, $sqlColumns, $sqlValues);
        if (!file_put_contents($sqlPath, $sqlResult)) {
            throw new SourceFileException("Не удалось экспортировать данные в файл {$sqlPath}");
        }
    }

}
