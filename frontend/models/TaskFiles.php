<?php


namespace frontend\models;


use yii\db\ActiveRecord;
use yii\web\UploadedFile;


/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 *  @property int task_id
 * @property string $file
 */

class TaskFiles extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $files;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 4],
        ];
    }

    public function attributeLabels()
    {
        return ['files' => 'Файлы'];
    }

    /*public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('/img/' . $this->file->baseName . '.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }*/
}