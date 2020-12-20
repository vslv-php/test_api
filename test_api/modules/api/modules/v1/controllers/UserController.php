<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\AuthUser;
use app\models\UserToken;
use app\modules\api\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class UserController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['login'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                    ],
                ],
            ]
        );
    }
    
    /**
     * Аутентификация пользователя
     *
     * @return array
     */
    public function actionLogin(): array
    {
        if (!Yii::$app->request->isPost) {
            return $this->error(405, 'Метод не поддерживается');
        }
        
        ['email' => $email, 'password' => $password] = Yii::$app->request->post();
        
        $user = AuthUser::findOne(['email' => $email]);
        if (!$user) {
            return $this->error(404, sprintf('Пользователь с email "%s" не найден', $email));
        }
        
        if ($user->validatePassword($password)) {
            $token = $user->token;
            if (!$token) {
                $token = new UserToken(['user_id' => $user->id]);
            }
            
            $token->expired = UserToken::defaultExpired();
            if ($token->save()) {
                $userData = $user->toArray(['email', 'name', 'phone']);
                $userData['type'] = $user->type->type;
                return [
                    'success' => true,
                    'user' => $userData,
                    'token' => $token->toArray(['token', 'expired'])
                ];
            }
            
            return $this->error(500, 'Ошибка сохранения токена');
        }
        
        return $this->error(401, 'Недействительные логин/пароль');
    }
}