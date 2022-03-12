<?php


namespace frontend\models;


use yii\db\ActiveRecord;


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
    public $files = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'task_id'], 'safe'],
            ['name', 'string', 'min' => 1, 'max' => 100,
                'tooShort' => "Имя файла не менее {min} символов", 'tooLong' => 'Имя файла не более {max} символов'],
            ['url', 'string', 'min' => 1, 'max' => 255,
                'tooShort' => "Путь к файлу не менее {min} символов", 'tooLong' => 'Путь к файлу не более {max} символов'],
        ];
    }

    /**
     * Для всех привязанных к заданию файлов данный метод формирует новое название файла и
     * сохраняет этот файл в папке upload.
     */
    public function upload()
    {
        foreach ($this->files as $file) {
            $taskFile = new TaskFiles();
            $newName = uniqid(date('Y-m-d-')) . '.' . $file->getExtension();
            $file->saveAs('@webroot/uploads/' . $newName);
            $taskFile->task_id = $this->task_id;
        }
    }
}