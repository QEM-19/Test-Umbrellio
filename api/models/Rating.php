<?php

namespace api\models;

/**
 * Rating model
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $value
 */
class Rating extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%rating}}';
    }

    public function rules()
    {
        return [
            [['value'], 'integer', 'min' => 1, 'max' => 5],
            [
                ['post_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Post::className(),
                'targetAttribute' => ['post_id' => 'id']
            ]
        ];
    }
}
