<?php


namespace frontend\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadFilesForm extends Model
{
    public $files = [];
    public $avatar;

    const SCENARIO_CREATE_TASK = 'create_task';
    const SCENARIO_UPDATE_ACCOUNT = 'update_account';

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, docx, txt, pdf, doc, xls, csv',
                'maxSize' => 2048 * 2048, 'maxFiles' => 4, 'on' => self::SCENARIO_CREATE_TASK,
                'message' => 'Выбран неверный формат файла или слишком большой размер'],
            [['files'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg',
                'maxSize' => 2048 * 2048, 'maxFiles' => 6, 'on' => self::SCENARIO_UPDATE_ACCOUNT,
                'message' => 'Выбран неверный формат файла или слишком большой размер'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg',
                'maxSize' => 2048 * 2048, 'on' => self::SCENARIO_UPDATE_ACCOUNT,
                'message' => 'Выбран неверный формат файла или слишком большой размер']
        ];
    }

    public function attributeLabels()
    {
        return ['files' => 'Файлы', 'avatar' => 'Сменить аватар'];
    }

    public static function uploadFile(UploadedFile $file)
    {
        $newName = uniqid(date('Y-m-d-')) . '.' . $file->getExtension();
        return $file->saveAs('@webroot/uploads/' . $newName) ? $newName : false;
    }
}