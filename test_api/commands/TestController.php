<?php

namespace app\commands;

use app\models\AuthUser;
use yii\console\Controller;

/**
 * Заполнение тестовых данных
 */
class TestController extends Controller
{
    /**
     * Добавление тестовых пользователей
     */
    public function actionAddUsers(): void
    {
        $emails = [
            'test@test.com',
            'alice@gmail.com',
            'bob@mail.ru',
        ];
        
        foreach ($emails as $email) {
            $user = new AuthUser(
                [
                    'email' => $email,
                    'password' => '123456',
                    'type_id' => rand(1, 2)
                ]
            );
            if (!$user->save()) {
                print_r($user->errors);
            }
        }
    }
}