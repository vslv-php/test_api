<?php

use yii\db\Migration;

class m201220_121649_create_user_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'expired' => $this->dateTime()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_token-user_id}}',
            '{{%user_token}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_token-user_id}}',
            '{{%user_token}}',
            'user_id',
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
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_token-user_id}}',
            '{{%user_token}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_token-user_id}}',
            '{{%user_token}}'
        );

        $this->dropTable('{{%user_token}}');
    }
}
