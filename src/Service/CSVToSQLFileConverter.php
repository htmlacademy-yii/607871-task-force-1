<?php


namespace App\Service;


use App\Exception\DataException;
use App\Exception\SourceFileException;

class CSVToSQLFileConverter
{

    public static function convert(string $fileName, string $dirName, string $tableName, string $delimiter = ',', array $extraColumns = [], $callback = null)
    {
        $fileObject = BaseFileInspector::checkFileAvailability($fileName, 'csv');

        $csvHeaders = self::getHeaders($fileObject, $delimiter);

        $sqlFile = $fileObject->getBasename('.csv') . '.sql';

        if ($callback && !is_callable($callback)) {
            throw new DataException("Дополнительные данные не могут быть добавлены в файл {$sqlFile}, проверьте callback параметр.");
        }

        $values = [];
        $extraValues = $callback && is_callable($callback);

        foreach (self::getNextLine($fileObject, $delimiter) as $csvLine) {

            $valuesLine = $extraValues ? array_merge($csvLine, call_user_func($callback)): $csvLine;

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

    private static function getHeaders(\SplFileObject $fileObject, string $delimiter)
    {
        $fileObject->rewind();
        return $fileObject->fgetcsv($delimiter);
    }

    private static function getNextLine(\SplFileObject $fileObject, string $delimiter): ?iterable
    {
        $result = null;
        while (!$fileObject->eof()) {
            $csvLine = $fileObject->fgetcsv($delimiter);
            if (!array_filter($csvLine)) {
                continue;
            }
            yield $csvLine;
        }
        return $result;
    }

}
