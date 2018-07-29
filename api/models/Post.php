<?php

namespace api\models;

/**
 * Post model
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $title
 * @property string $text
 * @property string $ip_author
 */
class Post extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%post}}';
    }

    public function rules()
    {
        return [
            [['title', 'text', 'ip_author', 'author_id'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['text'], 'string', 'max' => 10000],
            [['ip_author'], 'string', 'max' => 100],
            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Author::className(),
                'targetAttribute' => ['author_id' => 'id']
            ]
        ];
    }
}
