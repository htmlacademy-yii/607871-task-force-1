<?php


namespace App\Service;


use App\Exception\FileFormatException;
use App\Exception\SourceFileException;

class DataImporter
{
    private $fileName;
    private $columns;
    private $fileObject;

    private $result = [];
    private $error = null;

    public function __construct(string $fileName, array $columns)
    {
        $this->fileName = $fileName;
        $this->columns = $columns;
    }

    public function import(): void
    {
        if (!file_exists($this->fileName)) {
            throw new SourceFileException("Файл {$this->fileName} не существует");
        }

        if (!$this->validateColumns($this->columns)) {
            throw new FileFormatException('Заданы неверные заголовки столбцов для иморта');
        }

        try {
            $this->fileObject = new \SplFileObject($this->fileName);
        } catch (\RuntimeException $exception) {
            throw new SourceFileException("Не удалось открыть файл {$this->fileName} на чтение");
        } catch (\LogicException $exception) {
            throw new SourceFileException("{$this->fileName} является каталогом");
        }

        $header_data = $this->getHeaderData();

        if ($header_data !== $this->columns) {
            throw new FileFormatException("Файл {$this->fileName} содержит неверный перечень столбцов");
        }

        foreach ($this->getNextLine() as $line) {
            $this->result[] = $line;
        }
    }

    public function getData(): array
    {
        return $this->result;
    }

    private function getHeaderData(): ?array
    {
        $this->fileObject->rewind();
        $data = $this->fileObject->fgetcsv();

        return $data;
    }

    private function getNextLine(): ?iterable
    {
        $result = null;

        while(!$this->fileObject->eof()) {
            yield $this->fileObject->fgetcsv();
        }
        return $result;
    }

    private function validateColumns(array $columns): bool
    {
        $result = true;

        if (count($columns)) {
            foreach ($columns as $column) {
                if (!is_string ($column)) {
                    $result = false;
                }
            }
        } else {
            $result = false;
        }

        return $result;
    }
}
