<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Пользователь
 *
 * @property int         $id
 * @property string      $email
 * @property string      $password
 * @property string|null $name
 * @property string|null $phone
 * @property int         $type_id
 *
 * @property UserType    $type
 * @property UserToken   $token
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'password', 'type_id'], 'required'],
            [['type_id'], 'default', 'value' => null],
            [['type_id'], 'integer'],
            [['email', 'password', 'name', 'phone'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [
                ['type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UserType::class,
                'targetAttribute' => ['type_id' => 'id']
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'name' => 'Name',
            'phone' => 'Phone',
            'type_id' => 'Type ID',
        ];
    }
    
    /**
     * Тип пользователя
     *
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(UserType::class, ['id' => 'type_id']);
    }
    
    /**
     * Токен пользователя
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getToken(): ActiveQuery
    {
        return $this->hasOne(UserToken::class, ['id' => 'id'])
                    ->viaTable(
                        UserToken::tableName(),
                        ['user_id' => 'id'],
                        function (ActiveQuery $query) {
                            $query->andWhere(['>', 'expired', new Expression('now()')]);
                        }
                    );
    }
}
