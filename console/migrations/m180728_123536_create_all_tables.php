<?php

use yii\db\Migration;

/**
 * Class m180728_123536_create_all_tables
 */
class m180728_123536_create_all_tables extends Migration
{
    public function up()
    {
        $this->createTable('{{%author}}', [
            '[[id]]' => $this->primaryKey(),
            '[[login]]' => $this->string(255),
        ]);

        $this->createTable('{{%post}}', [
            '[[id]]' => $this->primaryKey(),
            '[[author_id]]' => $this->integer(),
            '[[title]]' => $this->text(),
            '[[text]]' => $this->text(),
            '[[ip_author]]' => $this->string(255)
        ]);

        $this->addForeignKey(
            'post_author_id',
            '{{%post}}',
            '[[author_id]]',
            '{{%author}}',
            '[[id]]'
        );

        $this->createTable('{{%rating}}', [
            '[[id]]' => $this->primaryKey(),
            '[[post_id]]' => $this->integer(),
            '[[value]]' => $this->integer(1),
        ]);

        $this->addForeignKey(
            'rating_post_id',
            '{{%rating}}',
            '[[post_id]]',
            '{{%post}}',
            '[[id]]'
        );
    }

    public function down()
    {

    }
}
