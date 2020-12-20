<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Заявка
 *
 * @property int                $id
 * @property int                $order_id
 * @property int                $author_id
 * @property string             $status
 * @property string|null        $created
 *
 * @property Order              $order
 * @property User               $author
 * @property RequestStatusLog[] $statusLog
 */
class Request extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'request';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'author_id', 'status'], 'required'],
            [['order_id', 'author_id'], 'default', 'value' => null],
            [['order_id', 'author_id'], 'integer'],
            [['created'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => RequestStatus::statuses()],
            [
                ['order_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Order::class,
                'targetAttribute' => ['order_id' => 'id']
            ],
            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'author_id' => 'Author ID',
            'status' => 'Status',
            'created' => 'Created',
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
    
    /**
     * Автор заявки
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }
    
    /**
     * Журнал изменения статусов
     *
     * @return ActiveQuery
     */
    public function getStatusLog(): ActiveQuery
    {
        return $this->hasMany(RequestStatusLog::class, ['request_id' => 'id']);
    }
    
    /**
     * {@inheritDoc}
     */
    public function beforeValidate(): bool
    {
        if ($this->isNewRecord) {
            $this->status = $this->status ?? RequestStatus::STATUS_NEW;
            $this->author_id = $this->author_id ?? Yii::$app->user->identity->getId();
        }
        return parent::beforeValidate();
    }
    
    /**
     * {@inheritDoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        $log = new RequestStatusLog(
            [
                'request_id' => $this->id,
                'status' => $this->status
            ]
        );
        $log->save();
        parent::afterSave($insert, $changedAttributes);
    }
}
