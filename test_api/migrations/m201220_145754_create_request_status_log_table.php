<?php

use yii\db\Migration;

class m201220_145754_create_request_status_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_status_log}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
            'assign_date' => $this->dateTime()->defaultExpression('now()'),
        ]);

        // creates index for column `request_id`
        $this->createIndex(
            '{{%idx-request_status_log-request_id}}',
            '{{%request_status_log}}',
            'request_id'
        );

        // add foreign key for table `{{%request}}`
        $this->addForeignKey(
            '{{%fk-request_status_log-request_id}}',
            '{{%request_status_log}}',
            'request_id',
            '{{%request}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%request}}`
        $this->dropForeignKey(
            '{{%fk-request_status_log-request_id}}',
            '{{%request_status_log}}'
        );

        // drops index for column `request_id`
        $this->dropIndex(
            '{{%idx-request_status_log-request_id}}',
            '{{%request_status_log}}'
        );

        $this->dropTable('{{%request_status_log}}');
    }
}
