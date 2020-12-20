<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Журнал изменения статусов
 *
 * @property int         $id
 * @property int         $request_id
 * @property string      $status
 * @property string|null $assign_date
 *
 * @property Request     $request
 */
class RequestStatusLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'request_status_log';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['request_id', 'status'], 'required'],
            [['request_id'], 'default', 'value' => null],
            [['request_id'], 'integer'],
            [['assign_date'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => RequestStatus::statuses()],
            [
                ['request_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Request::class,
                'targetAttribute' => ['request_id' => 'id']
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
            'request_id' => 'Request ID',
            'status' => 'Status',
            'assign_date' => 'Assign Date',
        ];
    }
    
    /**
     * Заявка
     *
     * @return ActiveQuery
     */
    public function getRequest(): ActiveQuery
    {
        return $this->hasOne(Request::class, ['id' => 'request_id']);
    }
}
