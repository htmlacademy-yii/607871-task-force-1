<?php


namespace frontend\controllers;

use yii\web\Controller;

class SecuredController extends Controller
{
    /**
     * Метод, определяющий уровень доступа для залогиненных и незалогиненных пользователей.
     * @return array|array[]
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'controllers' => ['site', 'signup'],
                        'actions' => ['login', 'index'],
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/tasks');
                        }
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site', 'signup'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['tasks', 'users', 'location', 'my-list', 'account'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }
}