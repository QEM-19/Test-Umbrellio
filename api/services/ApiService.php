<?php

namespace api\services;

use api\helpers\StatusHelper;
use api\models\Author;
use api\models\Post;
use api\models\Rating;
use yii\db\ActiveRecord;
use yii\db\Query;

class ApiService
{
    public function help()
    {
        $response['list_commands'] = [
            'add-post [POST\'S TITLE] [POST\'S TEXT] [AUTHOR\'S LOGIN] [AUTHOR\'S IP]',
            'set-rate [POST\'S ID] [RATE VALUE]',
            'get-top-posts',
            'get-multiple-ip-authors'
        ];

        return $response;
    }

    public function addPost($title, $text, $login, $ip)
    {
        $authorModel = Author::find()->where(['login' => $login])->one();

        if (is_null($authorModel)) {
            $authorModel = new Author();
            $authorModel->login = $login;

            if (!$authorModel->save()) {
                return $this->decorateResponse(StatusHelper::STATUS_ERROR, $authorModel->errors);
            }
        }

        $model = new Post();
        $model->title = $title;
        $model->text = $text;
        $model->author_id = $authorModel->id;
        $model->ip_author = $ip;

        return $this->createResponse($model);
    }

    public function setRate($postId, $rateValue)
    {
        $model = new Rating();
        $model->post_id = $postId;
        $model->value = $rateValue;

        $response = $this->createResponse($model, true);

        if ($response) {
            return $this->getAverageRate($postId);

        } else {
            return $response;
        }
    }

    public function getTopPosts()
    {
        $postTable = Post::tableName();
        $ratingTable = Rating::tableName();

        $query = new Query();

        $posts = $query->select($postTable . '.title, ' . $postTable . '.text')
            ->from('post')
            ->join('INNER JOIN', $ratingTable, $postTable . '.id = ' . $ratingTable . '.post_id')
            ->groupBy($postTable . '.id')
            ->orderBy('AVG(' . $ratingTable . '.value) DESC')
            ->limit(10)
            ->all();

        return $posts;
    }

    public function getMultipleIpAuthors()
    {
        $postTable = Post::tableName();
        $authorTable = Author::tableName();

        $query = new Query();

        $models = $query->select($postTable . '.ip_author, ' . $authorTable . '.login')
            ->from($postTable)
            ->join('INNER JOIN', $authorTable, $postTable . '.author_id = ' . $authorTable . '.id')
            ->groupBy($postTable . '.ip_author, ' . $authorTable . '.login')
            ->all();

        $result = [];

        foreach ($models as $model) {
            $result[$model['ip_author']][] = $model['login'];
        }

        foreach ($result as $ip => $logins) {
            if (count($logins) < 2) {
                unset($result[$ip]);
            }
        }

        return $result;
    }

    private function getAverageRate($postId)
    {
        return round(Rating::find()->where(['post_id' => $postId])->average('value'), 2);
    }

    private function decorateResponse($status, $response)
    {
        return ['status' => $status, 'response' => $response];
    }

    private function createResponse(ActiveRecord $model, $customResponse = false)
    {
        if ($model->save()) {
            if ($customResponse) {
                return true;

            } else {
                return $this->decorateResponse(StatusHelper::STATUS_SUCCESS, $model->toArray());
            }

        } else {
            return $this->decorateResponse(StatusHelper::STATUS_ERROR, $model->errors);
        }
    }
}