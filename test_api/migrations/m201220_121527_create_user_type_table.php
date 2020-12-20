<?php

use app\models\UserType;
use yii\db\Migration;

class m201220_121527_create_user_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_type}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
        ]);
        
        $types = [
            [UserType::TYPE_PRIVATE],
            [UserType::TYPE_ENTITY],
        ];
        
        $this->batchInsert('{{%user_type}}', ['type'], $types);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_type}}');
    }
}
