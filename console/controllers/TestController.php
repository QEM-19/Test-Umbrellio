<?php

namespace console\controllers;

use api\models\Post;
use api\services\ApiService;
use yii\base\Module;
use yii\console\Controller;

class TestController extends Controller
{
    const COUNT_POSTS = 200000;
    const COUNT_AUTHORS = 100;
    const COUNT_RATES = 500;
    const COUNT_IPS = 50;

    private $service;
    private $curl;

    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->curl = new \maxwen\yii\curl\Curl();
        $curlOptions = [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
        ];
        $this->curl->options = $curlOptions;

        $this->service = new ApiService();
    }

    public function actionIndex()
    {
        echo 'Enter command' . PHP_EOL;
    }

    public function actionFillBase()
    {
        $ips = [];
        $logins = [];

        for ($i = 0; $i < self::COUNT_IPS; $i++) {
            $ips[] = $i . '.' . $i . '.' . $i . '.' . $i;
        }

        for ($i = 0; $i < self::COUNT_AUTHORS; $i++) {
            $logins[] = 'login' . $i;
        }


        for ($i = 0; $i < self::COUNT_AUTHORS; $i++) {
            for ($j = 0; $j < self::COUNT_POSTS / self::COUNT_AUTHORS; $j++) {
                $post = $this->service->addPost('title', 'text', $logins[$i], $ips[rand(0, self::COUNT_IPS - 1)]);

                if (rand(0, 5) === 0) {
                    $this->service->setRate($post['response']['id'], rand(1, 5));
                }
            }
        }
    }

    public function actionTestAll()
    {
        $this->actionTestAdd();
        $this->actionTestSetRate();
        $this->actionTestGetMultiple();
        $this->actionTestGetTopPosts();
    }

    public function actionTestAdd()
    {
        echo 'TEST AddPost()' . PHP_EOL;

        $rand = rand(9999999, 999999999);

        $startTime = microtime(true);
        $result = $this->curl->get(
            'http://umbrellio.local/api/add-post/',
            [
                'title' => 'title',
                'text' => 'text',
                'login' => 'login' . $rand,
                'ip' => $rand
            ]
        );
        $time = microtime(true) - $startTime;

        print_r($result->body);

        echo PHP_EOL . 'Time add with create author: ' . $time . PHP_EOL;

        $startTime = microtime(true);
        $result = $this->curl->get(
            'http://umbrellio.local/api/add-post/',
            [
                'title' => 'title',
                'text' => 'text',
                'login' => 'login' . $rand,
                'ip' => $rand
            ]
        );
        $time = microtime(true) - $startTime;

        print_r($result->body);

        echo PHP_EOL . 'Time add without create author: ' . $time . PHP_EOL;
    }

    public function actionTestSetRate()
    {
        echo 'TEST SetRate()' . PHP_EOL;

        $rand = rand(1, 5);
        $postId = Post::find()->one()->id;

        $startTime = microtime(true);
        $result = $this->curl->get(
            'http://umbrellio.local/api/set-rate/',
            [
                'postId' => $postId,
                'rateValue' => $rand,
            ]
        );
        $time = microtime(true) - $startTime;

        print_r($result->body);

        echo PHP_EOL . 'Time set rate: ' . $time . PHP_EOL;
    }

    public function actionTestGetTopPosts()
    {
        echo 'TEST GetTopPosts()' . PHP_EOL;

        $startTime = microtime(true);
        $result = $this->curl->get('http://umbrellio.local/api/get-top-posts/', []);
        $time = microtime(true) - $startTime;

        print_r($result->body);

        echo PHP_EOL . 'Time to get top posts: ' . $time . PHP_EOL;
    }

    public function actionTestGetMultiple()
    {
        echo 'TEST GetMultipleIpAuthors()' . PHP_EOL;

        $startTime = microtime(true);
        $result = $this->curl->get('http://umbrellio.local/api/get-multiple-ip-authors/', []);
        $time = microtime(true) - $startTime;

        print_r($result->body);

        echo PHP_EOL . 'Time to get multiple: ' . $time . PHP_EOL;
    }
}