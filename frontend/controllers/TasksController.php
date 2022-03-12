<?php


namespace frontend\controllers;

use App\core\action\CancelAction;
use App\core\action\DoneAction;
use App\core\action\RefuseAction;
use App\Exception\DataException;
use frontend\models\forms\CreateTaskForm;
use frontend\models\forms\FinishTaskForm;
use frontend\models\UserMessage;
use frontend\models\forms\TaskSearchForm;
use frontend\models\forms\UploadFilesForm;
use frontend\models\Respond;
use frontend\models\Task;
use frontend\models\User;
use frontend\service\NotificationService;
use frontend\service\TaskService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{
    /**
     * Метод отвечает за отображение страницы со списком заданий.
     * @return string
     */
    public function actionIndex()
    {
        $searchForm = new TaskSearchForm();
        $searchForm->load(Yii::$app->request->get());

        return $this->render('index', ['dataProvider' => $searchForm->getDataProvider(), 'model' => $searchForm]);
    }

    /**
     * Метод отвечает за страницу с отображением конкретного задания.
     * @param $id - id задания
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /** @var Task $task */
        $task = Task::find()
            ->with('client', 'executor')
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
        $userCard = TaskService::chooseUser($task);

        return $this->render('view', [
            'task' => $task,
            'model' => $searchForm,
            'userCard' => $userCard,
            'actions' => $actions,
        ]);
    }

    /**
     * Метод отвечает за отображение страницы "Создать задание" и за обработку данных, полученных из формы на этой странице.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $user = User::findOne(Yii::$app->user->id);
        if ($user->categories) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $createTaskForm = new CreateTaskForm();
        $uploadFilesModel = new UploadFilesForm(['scenario' => UploadFilesForm::SCENARIO_CREATE_TASK]);

        if (\Yii::$app->request->getIsPost()) {
            $createTaskForm->load(Yii::$app->request->post());
            $createTaskForm->validate();
            $uploadFilesModel->files = UploadedFile::getInstances($uploadFilesModel, 'files');
            $uploadFilesModel->validate();
            if (!$createTaskForm->errors && !$uploadFilesModel->errors && $task = $createTaskForm->saveFields($uploadFilesModel)) {
                return $this->redirect("/task/view/{$task->id}");
            }
        }

        return $this->render('create', [
            'uploadFiles' => $uploadFilesModel,
            'createTaskForm' => $createTaskForm,
        ]);
    }

    /**
     * Метод отвечающий за назначение исполнителя по заданию на основании его отклика.
     * @param int $taskId - id задания.
     * @param int $respondId - id отклика.
     * @return \yii\web\Response
     * @throws DataException
     */
    public function actionConfirm(int $taskId, int $respondId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($respondId);
        $executor = User::findOne($respond->volunteer->id);

        if (!$task || !$executor || !$respond || !CancelAction::getUserRightsCheck($task)) {
            throw new DataException('Данное действие не может быть выполнено!');
        }

        if (TaskService::executorConfirm($task, $respond, $executor)) {
            $notification = new NotificationService($task, $executor);
            $notification->inform(UserMessage::TYPE_TASK_CONFIRMED);
        };

        return $this->redirect("/task/view/{$taskId}");
    }

    /**
     * Метод отвечает за обработку отказа потенциальному исполнителю со стороны клиента.
     * @param int $taskId
     * @param int $respondId
     * @return \yii\web\Response
     */
    public function actionDeny(int $taskId, int $respondId)
    {
        $task = Task::findOne($taskId);
        $respond = Respond::findOne($respondId);
        if ($task && $respond && CancelAction::getUserRightsCheck($task)) {
            $respond->status = Respond::STATUS_REFUSED;
            $respond->save();
        }

        return $this->redirect("/task/view/{$taskId}");
    }

    /**
     * Метод отвечает за обработку отказа уже назначенного исполнителя от задания.
     * @return \yii\web\Response
     */
    public function actionRefuse()
    {
        if (Yii::$app->request->getIsPost()) {
            $task = Task::findOne(Yii::$app->request->post('Task')['id']);
            if ($task && RefuseAction::getUserRightsCheck($task)) {
                $task->status = Task::STATUS_FAILED;
                $task->save();
                $notification = new NotificationService($task, $task->client);
                $notification->inform(UserMessage::TYPE_TASK_FAILED);
                return $this->redirect("/task/view/{$task->id}");
            }
        }
        return $this->redirect("/tasks");
    }

    /**
     * Метод обрабатывает отмену клиентом еще не взятого в работу задания.
     * @return \yii\web\Response
     */
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

    /**
     * Метод отвечает за создание откликов по заданию со сторроны потнециальных исполнителей.
     * @return array|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionTaskRespond()
    {
        $respond = new Respond();
        if (\Yii::$app->request->isPost) {
            $respond->load(\Yii::$app->request->post());
            $respond->user_id = Yii::$app->user->id;
            $respond->status = Respond::STATUS_NEW;
            $respond->save();

            $task = Task::findOne($respond->task_id);
            $notification = new NotificationService($task, $task->client);
            $notification->inform(UserMessage::TYPE_TASK_RESPONDED);

            return $this->redirect("/task/view/{$respond->task_id}");
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if ($respond->load(\Yii::$app->request->post())) {
                return \yii\widgets\ActiveForm::validate($respond);
            }
        }
        throw new BadRequestHttpException('Неверный запрос!');
    }

    /**
     * Метод отвечает за завершение задания со стороны клиента и размещение отзыва о работе исполнителя.
     * @return array|\yii\web\Response
     * @throws DataException
     */
    public function actionTaskFinish()
    {
        $finishTaskForm = new FinishTaskForm();

        if (Yii::$app->request->getIsPost()) {
            if (!$finishTaskForm->load(Yii::$app->request->post()) || !$finishTaskForm->validate()) {
                throw new DataException('Данный отзыв не может быть размещен');
            }

            $task = Task::findOne($finishTaskForm->taskId);
            if (!$task || !DoneAction::getUserRightsCheck($task)) {
                throw new DataException('Недостаточно прав для выполнения данного действия');
            }
            if ($finishTaskForm->saveFields($task)) {
                $notification = new NotificationService($task, $task->executor);
                $notification->inform(UserMessage::TYPE_TASK_CLOSED);
                return $this->redirect("/task/view/{$task->id}");
            }
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$finishTaskForm->load(Yii::$app->request->post())) {
                return \yii\widgets\ActiveForm::validate($finishTaskForm);
            }
        }
        return $this->redirect("/tasks");
    }
}