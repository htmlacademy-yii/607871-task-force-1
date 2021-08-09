<?php


namespace App\Service;


use App\Exception\SourceFileException;

abstract class DataBaseImporter
{
    protected $csvFile;
    protected $requiredFileColumns = [];
    protected $dbTableColumns = [];
    protected $filePath = CSV_CATALOG;


    public function __construct(string $fileName)
    {
        $this->csvFile = new CSVToSQLFileConverter($fileName, $this->requiredFileColumns);
    }

    abstract protected function getTableName();

    public function importFromFileCSV(): void
    {


        $sqlResult = "INSERT INTO `{$this->getTableName()}` ({$this->prepareTableColumns()}) VALUES \n{$this->prepareFileValues()};";

        $exportResult = BaseFileInspector::createFileObject($sqlFile)->openFile('w')->fwrite($sqlResult);

        if (!$exportResult) {
            throw new SourceFileException("Не удалось выполнить импорт данных в файл {$sqlFile}");
        }

    }

    protected function prepareTableColumns(): string
    {
        $columns = [];
        foreach ($this->dbTableColumns as $column => $value) {
            $columns[] = '`' . $column . '`';
        }

        return implode(', ', $columns);
    }

    protected function prepareFileValues()
    {

        foreach ($this->csvFile->getNextLine() as $line) {
            if (!empty($line)) {
                $csvArray = array_merge($this->dbTableColumns, array_combine($this->requiredFileColumns, $line));
                $sqlValues = [];
                foreach ($csvArray as $key => $value) {
                    $sqlValues[] = ($value !== NULL) ? "'" . $value . "'" : 'NULL';
                }
                $sqlPart .= "(" . implode(", ", $sqlValues) . "), \n";
            }
        }

        return rtrim($sqlPart, ", \n");
    }
}
