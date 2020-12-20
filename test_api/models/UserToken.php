<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Токен пользователя
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $expired
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_token';
    }
    
    /**
     * Срок действия токена по умолчанию
     *
     * @return string
     */
    public static function defaultExpired(): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 week'));
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'token', 'expired'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['expired'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['token'], 'unique'],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
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
            'user_id' => 'User ID',
            'token' => 'Token',
            'expired' => 'Expired',
        ];
    }

    /**
     * Пользователь
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function beforeValidate(): bool
    {
        $this->token = $this->token ?? Yii::$app->security->generateRandomString();
        $this->expired = $this->expired ?? self::defaultExpired();
        return parent::beforeValidate();
    }
}
