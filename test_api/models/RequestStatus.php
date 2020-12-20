<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Статус заявки
 */
class RequestStatus extends ActiveRecord
{
    public const STATUS_NEW = 'Новая';
    public const STATUS_IN_PROGRESS = 'В работе';
    public const STATUS_DONE = 'Выполнена';
    
    /**
     * @return string[]
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE
        ];
    }
}
