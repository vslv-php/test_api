<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Заказ
 *
 * @property int              $id
 * @property string           $title
 * @property string           $description
 * @property string           $deadline
 * @property int              $broker_percent
 * @property int              $author_id
 * @property int|null         $executor_id
 * @property string           $status
 * @property string|null      $created
 *
 * @property User             $author
 * @property OrderStatusLog[] $statusLog
 * @property Request[]        $requests
 * @property Request          $appliedRequest
 */
class Order extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'description', 'deadline', 'broker_percent', 'author_id', 'status'], 'required'],
            [['description'], 'string'],
            [['deadline', 'created'], 'safe'],
            [['broker_percent', 'author_id', 'executor_id'], 'default', 'value' => null],
            [['broker_percent', 'author_id', 'executor_id'], 'integer'],
            [['title', 'status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => OrderStatus::statuses()],
            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id']
            ],
            [
                ['executor_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['executor_id' => 'id']
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
            'title' => 'Title',
            'description' => 'Description',
            'deadline' => 'Deadline',
            'broker_percent' => 'Broker Percent',
            'author_id' => 'Author ID',
            'executor_id' => 'Executor ID',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }
    
    /**
     * Создатель заказа
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
        return $this->hasMany(OrderStatusLog::class, ['order_id' => 'id']);
    }
    
    /**
     * Заявки
     *
     * @return ActiveQuery
     */
    public function getRequests(): ActiveQuery
    {
        return $this->hasMany(Request::class, ['order_id' => 'id']);
    }
    
    /**
     * Принятая заявка
     *
     * @return ActiveQuery
     */
    public function getAppliedRequest(): ActiveQuery
    {
        return $this->hasOne(Request::class, ['id' => 'id'])->via('requests', function (ActiveQuery $query) {
            $query->andWhere(['request.status' => RequestStatus::STATUS_IN_PROGRESS]);
        });
    }
    
    /**
     * Принять заявку
     *
     * @param Request $request
     *
     * @return bool
     */
    public function applyRequest(Request $request): bool
    {
        if ($request->order_id !== $this->id || $this->appliedRequest) {
            return false;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        $request->status = RequestStatus::STATUS_IN_PROGRESS;
        $requestSaved = $request->save();
        
        $this->status = OrderStatus::STATUS_IN_PROGRESS;
        $orderSaved = $this->save();
    
        try {
            $transaction->commit();
            return $requestSaved && $orderSaved;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }
    
    /**
     * Отметить как "Выполнен"
     *
     * @return bool
     */
    public function setDone(): bool
    {
        $request = $this->appliedRequest;
        if (!$request) {
            return false;
        }
    
        $transaction = Yii::$app->db->beginTransaction();
        
        $this->status = OrderStatus::STATUS_DONE;
        $orderSaved = $this->save();
        
        $request->status = RequestStatus::STATUS_DONE;
        $requestSaved = $request->save();
    
        try {
            $transaction->commit();
            return $requestSaved && $orderSaved;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->status = $this->status ?? OrderStatus::STATUS_SEARCH;
            $this->author_id = $this->author_id ?? Yii::$app->user->identity->getId();
        }
        return parent::beforeValidate();
    }
    
    /**
     * {@inheritDoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        $log = new OrderStatusLog(
            [
                'order_id' => $this->id,
                'status' => $this->status
            ]
        );
        $log->save();
        parent::afterSave($insert, $changedAttributes);
    }
}
