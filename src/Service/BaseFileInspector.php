<?php


namespace App\Service;


use App\Exception\FileFormatException;
use App\Exception\SourceFileException;

abstract class BaseFileInspector
{
    public static function checkFileAvailability(string $fileName, string $extension): ?\SplFileObject
    {
        self::checkFileExistance($fileName);
        $fileObject = self::createFileObject($fileName);
        self::checkFileExtension($fileObject, $extension);

        return $fileObject;
    }

    private static function checkFileExistance($fileName): void
    {
        if (!file_exists($fileName)) {
            throw new SourceFileException("Файл {$fileName} не существует");
        }
    }

    public static function checkFileExtension(\SplFileObject $fileObject, string $extension): void
    {
        $fileExtension = $fileObject->getExtension();
        if ($fileExtension !== $extension) {
            throw new FileFormatException("Некорректный формат файла {$fileObject->getBasename()}. Требуется расширение {$extension}.");
        }
    }

    public static function createFileObject($fileName): ?\SplFileObject
    {
        try {
            $fileObject = new \SplFileObject($fileName);
        } catch (\RuntimeException $exception) {
            throw new SourceFileException("Не удалось открыть файл {$fileName}");
        } catch (\LogicException $exception) {
            throw new SourceFileException("{$fileName} является каталогом");
        }
        return $fileObject;
    }
}
