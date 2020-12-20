<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Тип пользователя
 *
 * @property int $id
 * @property string $type
 *
 * @property User[] $users
 */
class UserType extends ActiveRecord
{
    public const TYPE_PRIVATE = 'Частное лицо';
    public const TYPE_ENTITY = 'Юр. лицо';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_type';
    }
    
    /**
     * Типы пользователя
     *
     * @return string[]
     */
    public static function types()
    {
        return [
            self::TYPE_PRIVATE,
            self::TYPE_ENTITY
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => self::types()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * Пользователи
     *
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['type_id' => 'id']);
    }
}
