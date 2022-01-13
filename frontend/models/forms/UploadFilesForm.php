<?php


namespace frontend\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadFilesForm extends Model
{
    public $files = [];

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, docx, txt, pdf, doc, xls, csv',
                'maxSize' => 2048 * 2048, 'maxFiles' => 4, 'message' => 'Выбран неверный формат файла'],
        ];
    }

    public function attributeLabels()
    {
        return ['files' => 'Файлы'];
    }

    public static function uploadFile(UploadedFile $file)
    {
        $newName = uniqid(date('Y-m-d-')) . '.' . $file->getExtension();
       return $file->saveAs('@webroot/uploads/' . $newName) ? $newName : false;
    }
}