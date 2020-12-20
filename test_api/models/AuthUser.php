<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\web\IdentityInterface;

class AuthUser extends User implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?self
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        /** @var self $user */
        $user = self::find()->alias('u')
                   ->leftJoin('user_token t', 't.user_id = u.id')
                   ->where(['t.token' => $token])
                   ->one();
        return $user;
    }
    
    /**
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail(string $email): ?self
    {
        return self::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->token === $authKey;
    }
    
    /**
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }
}
