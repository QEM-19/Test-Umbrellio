<?php

namespace api\controllers;

use api\services\ApiService;
use yii\base\Module;
use yii\web\Controller;

class ApiController extends Controller
{
    private $service;

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = new ApiService();
    }

    public function actionIndex()
    {
        echo json_encode($this->service->help());

        return null;
    }

    public function actionAddPost($title = null, $text = null, $login = null, $ip = null)
    {
        echo json_encode($this->service->addPost($title, $text, $login, $ip));
    }

    public function actionSetRate($postId = null, $rateValue = null)
    {
        echo json_encode($this->service->setRate($postId, $rateValue));
    }

    public function actionGetTopPosts()
    {
        echo json_encode($this->service->getTopPosts());
    }

    public function actionGetMultipleIpAuthors()
    {
        echo json_encode($this->service->getMultipleIpAuthors());
    }
}