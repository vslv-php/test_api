<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpHeaderAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Базовый контроллер API
 */
class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => HttpHeaderAuth::class,
                    'except' => ['login']
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'contentNegotiator' => [
                    'class' => 'yii\filters\ContentNegotiator',
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                        'application/xml' => Response::FORMAT_XML,
                    ],
                ],
            ]
        );
    }
    
    /**
     * @param int          $statusCode
     * @param string|array $message
     *
     * @return array
     */
    public function error(int $statusCode, $message): array
    {
        Yii::$app->response->setStatusCode($statusCode);
        return [
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ]
        ];
    }
}