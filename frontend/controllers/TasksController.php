<?php


namespace frontend\controllers;

use App\Exception\DataException;
use frontend\models\forms\TaskSearchForm;
use frontend\models\forms\UploadFilesForm;
use frontend\models\Recall;
use frontend\models\Respond;
use frontend\models\Task;
use frontend\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use frontend\models\TaskFiles;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{

    public function actionIndex()
    {
        $searchForm = new TaskSearchForm();
        $searchForm->load(Yii::$app->request->get());
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
        try {
            $actions = \App\business\Task::getPossibleActions($task);
        } catch (DataException $e) {
            $actions = [];
        }

        $searchForm = new TaskSearchForm();

        return $this->render('view', [
            'task' => $task,
            'model' => $searchForm,
            'actions' => $actions,
        ]);
    }

    public function actionCreate()
    {

        $task = new Task(['scenario' => Task::SCENARIO_CREATE_TASK]);
        $uploadFilesModel = new UploadFilesForm();

        if (\Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());
            $task->client_id = Yii::$app->user->getId();
            $task->city_id = 2;

            $uploadFilesModel->files = UploadedFile::getInstances($uploadFilesModel, 'files');
            $isValid = $task->validate();
            $isValid = $uploadFilesModel->validate() && $isValid;

            if ($isValid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $task->save();

                    foreach ($uploadFilesModel->files as $file) {
                        $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
                        if ($newFileName) {
                            $taskFile = new TaskFiles();
                            $taskFile->task_id = $task->primaryKey;
                            $taskFile->name = $file->name;
                            $taskFile->url = '/uploads/' . $newFileName;
                            $taskFile->save();
                        }
                    }

                    $transaction->commit();

                    return $this->redirect("/task/view/{$task->primaryKey}");
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', ['task' => $task, 'uploadFiles' => $uploadFilesModel]);
    }

    public function actionConfirm(int $taskId, int $messageId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($messageId);
        $executor = User::findOne($respond->volunteer->id);

        if ($task && $executor && $respond && Yii::$app->user->id == $task->client_id && $task->status == Task::STATUS_NEW) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $task->status = Task::STATUS_IN_PROGRESS;
                $task->executor_id = $executor->id;
                $respond->status = Respond::STATUS_CONFIRMED;
                $task->save();
                $respond->save();
                $transaction->commit();
            } catch (\Throwable $e) {
                $transaction->rollBack();
            }

        }
        return $this->redirect("/task/view/{$taskId}");
    }

    public function actionDeny(int $taskId, int $messageId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($messageId);
        if ($task && $respond && Yii::$app->user->id == $task->client_id) {
            $respond->status = Respond::STATUS_REFUSED;
            $respond->save();
        }

        return $this->redirect("/task/view/{$taskId}");
    }

    public function actionRefuse()
    {
        if (Yii::$app->request->getIsPost()) {
            $task = Task::findOne(Yii::$app->request->post('Task')['id']);
            if ($task && Yii::$app->user->id == $task->executor_id && $task->status == Task::STATUS_IN_PROGRESS) {
                $task->status = Task::STATUS_FAILED;
                $task->save();
                return $this->redirect("/task/view/{$task->id}");
            }
        }
        return $this->redirect("/tasks");
    }

    public function actionCancel()
    {
        if (Yii::$app->request->getIsPost()) {
            $task = Task::findOne(Yii::$app->request->post('Task')['id']);
            if ($task && Yii::$app->user->id == $task->client_id && $task->status == Task::STATUS_NEW) {
                $task->status = Task::STATUS_CANCELED;
                $task->save();
                return $this->redirect("/task/view/{$task->id}");
            }
        }
        return $this->redirect("/tasks");
    }

    public function actionTaskRespond()
    {
        $respond = new Respond();
        if (\Yii::$app->request->isPost) {
            $respond->load(\Yii::$app->request->post());
            $respond->user_id = Yii::$app->user->id;
            $respond->save();
            return $this->redirect("/task/view/{$respond->task_id}");
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($respond->load(\Yii::$app->request->post())) {
                return \yii\widgets\ActiveForm::validate($respond);
            }
        }
        throw new \yii\web\BadRequestHttpException('Неверный запрос!');
    }

    public function actionTaskFinish()
    {
        if (Yii::$app->request->getIsPost()) {
            $recall = new Recall();
            if ($recall->load(Yii::$app->request->post()) && $recall->validate()) {
                $task = Task::findOne($recall->task_id);
                if (Yii::$app->user->id == $task->client_id) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $task->status = $recall->taskStatus;
                        $task->save();
                        $recall->save();
                        $transaction->commit();
                        return $this->redirect("/task/view/{$recall->task_id}");
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }
        return $this->redirect("/tasks");
    }
}