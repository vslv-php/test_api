<?php

use yii\db\Migration;

class m201220_121639_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'name' => $this->string(),
            'phone' => $this->string(),
            'type_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `type_id`
        $this->createIndex(
            '{{%idx-user-type_id}}',
            '{{%user}}',
            'type_id'
        );

        // add foreign key for table `{{%user_type}}`
        $this->addForeignKey(
            '{{%fk-user-type_id}}',
            '{{%user}}',
            'type_id',
            '{{%user_type}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user_type}}`
        $this->dropForeignKey(
            '{{%fk-user-type_id}}',
            '{{%user}}'
        );

        // drops index for column `type_id`
        $this->dropIndex(
            '{{%idx-user-type_id}}',
            '{{%user}}'
        );

        $this->dropTable('{{%user}}');
    }
}
