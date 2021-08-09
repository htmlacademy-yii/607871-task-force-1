<?php


namespace App\Service;


use App\Exception\SourceFileException;

class CategoryImporter extends DataBaseImporter
{
    protected $requiredFileColumns = ['name', 'icon'];
    protected $dbTableColumns = ['id' => NULL, 'name' => NULL, 'icon' => NULL];

   /* public function __construct(string $fileName)
    {
        parent::__construct($fileName);
    }*/

    protected function getTableName()
    {
        return 'category';
    }
}
