<?php

use yii\db\Migration;

class m201220_145637_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
            'created' => $this->dateTime()->defaultExpression('now()'),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            '{{%idx-request-order_id}}',
            '{{%request}}',
            'order_id'
        );

        // add foreign key for table `{{%order}}`
        $this->addForeignKey(
            '{{%fk-request-order_id}}',
            '{{%request}}',
            'order_id',
            '{{%order}}',
            'id',
            'CASCADE'
        );

        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-request-author_id}}',
            '{{%request}}',
            'author_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-request-author_id}}',
            '{{%request}}',
            'author_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%order}}`
        $this->dropForeignKey(
            '{{%fk-request-order_id}}',
            '{{%request}}'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            '{{%idx-request-order_id}}',
            '{{%request}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-request-author_id}}',
            '{{%request}}'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            '{{%idx-request-author_id}}',
            '{{%request}}'
        );

        $this->dropTable('{{%request}}');
    }
}
