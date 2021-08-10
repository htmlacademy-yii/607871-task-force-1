<?php


namespace App\Service;


use App\Exception\DataException;
use App\Exception\SourceFileException;

class CSVToSQLFileConverter
{
    /**
     * @param string $fileName - полное имя csv-файла, из которого экспортируются данные
     * @param string $dirName - адрес директории, куда будет создаваться sql-файл
     * @param string $tableName - имя таблицы в БД, куда будут импортированы данные
     * @param string $delimiter - разделитель данныех в csv-файле.
     * @param array $extraColumns - названия дополнительных столбцов с данными для импорта
     * @param null $callback - функция, возвращающая массив значений для дополнительных столбцов с данными
     * @throws DataException - исключения, выбрасываемые из-за проблем с данными
     * @throws SourceFileException - исключения, выбрасываемые из-за проблем с доступом к файлу/директории
     */
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

    /** Забирает заголовки из csv-файлов
     * @param \SplFileObject $fileObject
     * @param string $delimiter - разделитель данныех в csv-файле.
     * @return array - массив с заголовками столбцов из csv-файла.
     */

    private static function getHeaders(\SplFileObject $fileObject, string $delimiter): array
    {
        $fileObject->rewind();
        $headers = $fileObject->fgetcsv($delimiter);
        if (!array_filter($headers)) {
            throw new DataException("Ошибка данных: файл {$fileObject->getBaseName()} не содержит заголовков.");
        }
        return $headers;
    }

    /** Забирает строки из csv-файла
     * @param \SplFileObject $fileObject - объект класса SplFileObject, созданный на основании csv-файла.
     * @param string $delimiter - разделитель данныех в csv-файле.
     * @return iterable|null - последовательно выводит данные по каждой строке файла, при достижении конца csv-файла, возвращает null.
     */

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
