<?php

use yii\db\Migration;

class m201220_141037_create_order_status_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_status_log}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
            'assign_date' => $this->dateTime()->defaultExpression('now()'),
        ]);

        // creates index for column `order_id`
        $this->createIndex(
            '{{%idx-order_status_log-order_id}}',
            '{{%order_status_log}}',
            'order_id'
        );

        // add foreign key for table `{{%order}}`
        $this->addForeignKey(
            '{{%fk-order_status_log-order_id}}',
            '{{%order_status_log}}',
            'order_id',
            '{{%order}}',
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
            '{{%fk-order_status_log-order_id}}',
            '{{%order_status_log}}'
        );

        // drops index for column `order_id`
        $this->dropIndex(
            '{{%idx-order_status_log-order_id}}',
            '{{%order_status_log}}'
        );

        $this->dropTable('{{%order_status_log}}');
    }
}
