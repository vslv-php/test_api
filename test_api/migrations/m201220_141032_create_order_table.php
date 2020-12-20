<?php

use yii\db\Migration;

class m201220_141032_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'deadline' => $this->dateTime()->notNull(),
            'broker_percent' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'executor_id' => $this->integer(),
            'status' => $this->string()->notNull(),
            'created' => $this->dateTime(),
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-order-author_id}}',
            '{{%order}}',
            'author_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-order-author_id}}',
            '{{%order}}',
            'author_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `executor_id`
        $this->createIndex(
            '{{%idx-order-executor_id}}',
            '{{%order}}',
            'executor_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-order-executor_id}}',
            '{{%order}}',
            'executor_id',
            '{{%user}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-order-author_id}}',
            '{{%order}}'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            '{{%idx-order-author_id}}',
            '{{%order}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-order-executor_id}}',
            '{{%order}}'
        );

        // drops index for column `executor_id`
        $this->dropIndex(
            '{{%idx-order-executor_id}}',
            '{{%order}}'
        );

        $this->dropTable('{{%order}}');
    }
}
