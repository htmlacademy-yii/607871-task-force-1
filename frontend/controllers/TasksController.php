<?php


namespace frontend\controllers;

use frontend\models\forms\TaskSearchForm;
use frontend\models\Task;
use yii\web\NotFoundHttpException;
use frontend\models\TaskFiles;
use yii\web\Response;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{
    public function beforeAction($action)
    {
        if ($this->action->id == 'upload') {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchForm = new TaskSearchForm();
        $searchForm->load(\Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $searchForm->getDataProvider(), 'model' => $searchForm]);
    }

    public function actionView($id)
    {
        $task = Task::find()
            ->with('client')
            ->where('task.id =:id', ['id' => $id])
            ->one();

        if (!$task) {
            throw new NotFoundHttpException("Задача не найдена!");
        }

        $searchForm = new TaskSearchForm();
        return $this->render('view', [
            'task' => $task,
            'model' => $searchForm,
        ]);
    }

    public function actionCreate()
    {
        $task = new Task();
        $taskFiles = new TaskFiles();

        if (!\Yii::$app->user->isGuest && \Yii::$app->request->getIsPost()) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $task->load(\Yii::$app->request->post());
            $task->client_id = \Yii::$app->user->getId();
            $task->status = Task::STATUS_NEW;
            $task->city_id = 2;
            $taskFiles->files = UploadedFile::getInstancesByName('files');

            $task->validate();
            $taskFiles->validate();
            if ($task->save()) {
                $taskFiles->task_id = $task->primaryKey;
                foreach ($taskFiles->files as $file) {
                    $newName = uniqid(date('Y-m-d-')) . '.' . $file->getExtension();
                    $file->saveAs('@webroot/uploads' . $newName);
                    $taskFiles->name = $file->name;
                    $taskFiles->url = $newName;
                    $taskFiles->save();
                }

            }



               /*try {
            $transaction = \Yii::$app->$db->beginTransaction();
                    $user->save(false);
                    $userId = $user->getId();
                    $profile->setAttribute('user_id', $userId);
                    $profile->save(false);
                    $transaction->commit();
                    $this->redirect('/');
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }
            }*/

        }

        return $this->render('create', ['task' => $task, 'taskFiles' => $taskFiles]);
    }


}