<?php


namespace frontend\models;


use yii\db\ActiveRecord;
use yii\web\UploadedFile;


/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property int task_id
 * @property string $url
 * @property string $name
 */
class TaskFiles extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $file;
    public $files = [];

    public function rules()
    {
        return [
            [['files', 'name', 'url'], 'safe'],
            ['name', 'string', 'min' => 1, 'max' => 100,
               'tooShort' => "Имя файла не менее {min} символов", 'tooLong' => 'Имя файла не более {max} символов' ],
            ['url', 'string', 'min' => 1, 'max' => 255,
                'tooShort' => "Путь к файлу не менее {min} символов", 'tooLong' => 'Путь к файлу не более {max} символов' ],

            [['files'], 'file', 'skipOnEmpty' => true,   'extensions' => 'png, jpg, jpeg, docx, txt, pdf, doc, xls, csv',
                'maxSize' => 2048 * 2048, 'maxFiles' => 4],
        ];
    }

    public function attributeLabels()
    {
        return ['files' => 'Файлы'];
    }


        public function uploadFiles()
    {
        $taskFiles = new TaskFiles();
        $taskFiles->files = UploadedFile::getInstancesByName('files');


        return $taskFiles->validate();

    }


}