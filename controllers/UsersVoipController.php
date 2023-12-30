<?php

namespace app\controllers;

use Yii;
use app\models\UsersVoip;
use app\models\search\UsersVoipSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * UsersVoipController implements the CRUD actions for UsersVoip model.
 */
class UsersVoipController extends DefaultController
{
    public $modelClass = 'app\models\UsersVoip';
    public $modelSearchClass = 'app\models\search\UsersVoipSearch';   
}
