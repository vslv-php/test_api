<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Журнал изменения статусов
 *
 * @property int $id
 * @property int $order_id
 * @property int $status_id
 * @property string|null $assign_date
 *
 * @property Order $order
 */
class OrderStatusLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order_status_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order_id', 'status'], 'required'],
            [['order_id'], 'default', 'value' => null],
            [['order_id'], 'integer'],
            [['assign_date'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => OrderStatus::statuses()],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'assign_date' => 'Assign Date',
        ];
    }

    /**
     * Заказ
     *
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
