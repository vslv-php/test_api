<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\Request;
use app\modules\api\controllers\BaseController;
use Yii;

/**
 * Работа с заявкой
 */
class RequestController extends BaseController
{
    /**
     * Создание заявки
     *
     * @return array
     */
    public function actionCreate(): array
    {
        if (!Yii::$app->request->isPost) {
            return $this->error(405, 'Метод не поддерживается');
        }
        
        $request = new Request();
        
        if ($request->load(Yii::$app->request->post(), '') && $request->validate()) {
            $order = $request->order;
            if ($order->author_id === $request->author_id) {
                return $this->error(400, 'Создатель заказа не может разместить заявку к своему заказу');
            }
            
            $myRequests = Request::find()->where(['order_id' => $request->order_id, 'author_id' => $request->author_id])->count();
            if ($myRequests > 0) {
                return $this->error(400, 'Нельзя создать две заявки к одному заказу');
            }
            
            if ($request->save()) {
                return [
                    'success' => true,
                    'request' => $request->toArray()
                ];
            }
        }
        
        if ($request->errors) {
            return $this->error(422, ['Ошибка валидации' => $request->errors]);
        }
        
        return $this->error(500, 'Ошибка сохранения');
    }
    
    /**
     * Принятие заявки
     *
     * @return array
     */
    public function actionApply(): array
    {
        if (!Yii::$app->request->isPost) {
            return $this->error(405, 'Метод не поддерживается');
        }
        
        $request = Request::findOne(Yii::$app->request->post('id'));
        
        if (!$request) {
            return $this->error(404, 'Заявка не найдена');
        }
        
        $order = $request->order;
    
        if ($order->author_id !== Yii::$app->user->identity->getId()) {
            return $this->error(403, 'Действие запрещено');
        }
        
        if ($request->id === ($order->appliedRequest->id ?? null)) {
            return $this->error(400, 'Заявка уже принята');
        }
        
        if ($order->applyRequest($request)) {
            return [
                'success' => true,
                'request' => $request->toArray(),
                'order' => $order->toArray()
            ];
        }
        
        return $this->error(500, 'Не удалось принять заявку');
    }
}