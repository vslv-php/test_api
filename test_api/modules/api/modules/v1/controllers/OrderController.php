<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\Order;
use app\models\OrderStatus;
use app\modules\api\controllers\BaseController;
use Yii;

/**
 * Работа с заказом
 */
class OrderController extends BaseController
{
    /**
     * Создание заказа
     *
     * @return array
     */
    public function actionCreate(): array
    {
        if (!Yii::$app->request->isPost) {
            return $this->error(405, 'Метод не поддерживается');
        }
        
        $order = new Order();
        
        if ($order->load(Yii::$app->request->post(), '') && $order->save()) {
            return [
                'success' => true,
                'order' => $order->toArray()
            ];
        }
        
        if ($order->errors) {
            return $this->error(422, ['Ошибка валидации' => $order->errors]);
        }
        
        return $this->error(500, 'Ошибка сохранения');
    }
    
    /**
     * Выполнение заказа
     *
     * @return array
     */
    public function actionSetDone(): array
    {
        if (!Yii::$app->request->isPost) {
            return $this->error(405, 'Метод не поддерживается');
        }
        
       $order = Order::findOne(Yii::$app->request->post('id'));
        
        if (!$order) {
            return $this->error(404, 'Заказ не найден');
        }
        
        if ($order->author_id !== Yii::$app->user->identity->getId()) {
            return $this->error(403, 'Действие запрещено');
        }
        
        if ($order->status === OrderStatus::STATUS_DONE) {
            return $this->error(400, 'Заказ уже выполнен');
        }
        
        if ($order->setDone()) {
            return [
                'success' => true,
                'order' => $order->toArray()
            ];
        }
        
        return $this->error(500, 'Не удалось отметить заказ как выполненный');
    }
}