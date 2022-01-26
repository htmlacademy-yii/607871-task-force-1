<?php


namespace frontend\controllers;

use yii\web\Controller;

class SecuredController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'controllers' => ['main', 'signup'],
                        'actions' => ['login', 'index'],
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/tasks');
                        }
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['main', 'signup'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['tasks', 'users', 'location'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];

    }
}