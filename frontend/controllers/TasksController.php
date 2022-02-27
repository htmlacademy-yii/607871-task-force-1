<?php


namespace frontend\controllers;

use App\core\action\CancelAction;
use App\core\action\DoneAction;
use App\core\action\RefuseAction;
use App\Exception\DataException;
use frontend\models\City;
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
        /** @var Task $task */
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
        $city = new City();
        $uploadFilesModel = new UploadFilesForm(['scenario' => UploadFilesForm::SCENARIO_CREATE_TASK]);

        if (\Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());
            $task->client_id = Yii::$app->user->id;
            $city->load(Yii::$app->request->post());
            if ($city->name) {
                if (!$city->validate()) {
                    throw new DataException('Данный город не может быть указан в задании');
                }

                $taskCity = City::find()->where(['name' => $city->name])->one();
                if ($taskCity) {
                    $task->city_id = $taskCity->id;
                    $task->searchDistrict();
                }
            }

            $uploadFilesModel->files = UploadedFile::getInstances($uploadFilesModel, 'files');
            $isValid = $task->validate();
            $isValid = $uploadFilesModel->validate() && $isValid;

            if ($isValid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $task->status = Task::STATUS_NEW;
                    $task->save();

                    foreach ($uploadFilesModel->files as $file) {
                        $newFileName = UploadFilesForm::uploadFile($file); //загрузка файла из временной папки в uploads
                        if ($newFileName) {
                            $taskFile = new TaskFiles();
                            $taskFile->task_id = $task->primaryKey;
                            $taskFile->name = $file->name;
                            $taskFile->url = \Yii::$app->params['defaultUploadDirectory'] . $newFileName;
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

        return $this->render('create', ['task' => $task, 'uploadFiles' => $uploadFilesModel, 'city' => $city]);
    }

    public function actionConfirm(int $taskId, int $messageId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($messageId);
        $executor = User::findOne($respond->volunteer->id);

        if (!$task || !$executor || !$respond || !CancelAction::getUserRightsCheck($task)) {
            throw new DataException('Данное действие не может быть выполнено!');
        }

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

        return $this->redirect("/task/view/{$taskId}");
    }

    public function actionDeny(int $taskId, int $messageId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($messageId);
        if ($task && $respond && CancelAction::getUserRightsCheck($task)) {
            $respond->status = Respond::STATUS_REFUSED;
            $respond->save();
        }

        return $this->redirect("/task/view/{$taskId}");
    }

    public function actionRefuse()
    {
        if (Yii::$app->request->getIsPost()) {
            $task = Task::findOne(Yii::$app->request->post('Task')['id']);
            if ($task && RefuseAction::getUserRightsCheck($task)) {
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
            if ($task && CancelAction::getUserRightsCheck($task)) {
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
            $respond->status = Respond::STATUS_NEW;
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

            if (!$recall->load(Yii::$app->request->post()) || !$recall->validate()) {
                throw new DataException('Данный отзыв не может быть размещен');
            }

            $task = Task::findOne($recall->task_id);

            if (!$task || !DoneAction::getUserRightsCheck($task)) {
                throw new DataException('Недостаточно прав для выполнения данного действия');
            }

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
        return $this->redirect("/tasks");
    }
}