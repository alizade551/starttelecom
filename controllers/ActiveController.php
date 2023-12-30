<?php

namespace app\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;


class ActiveController extends \yii\rest\ActiveController
{
	public $modelClass = '';
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['only'] = ['create', 'update', 'delete'];
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::class
        ];

        return $behaviors;
    }

}
