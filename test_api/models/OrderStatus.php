<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Статус заказа
 */
class OrderStatus extends ActiveRecord
{
    public const STATUS_SEARCH = 'В поиске исполнителя';
    public const STATUS_IN_PROGRESS = 'В работе';
    public const STATUS_DONE = 'Выполнен';
    
    /**
     * @return string[]
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_SEARCH,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE
        ];
    }
}
