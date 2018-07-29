<?php

namespace api\models;

/**
 * Author model
 *
 * @property integer $id
 * @property string $login
 */
class Author extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%author}}';
    }

    public function rules()
    {
        return [
            ['login', 'string', 'length' => [6, 32]],
            [['login'], 'unique']
        ];
    }
}