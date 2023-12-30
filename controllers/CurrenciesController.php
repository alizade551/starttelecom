<?php

namespace app\controllers;

use Yii;
use app\models\Currencies;
use app\models\search\CurrenciesSearch;
use app\components\DefaultController;
/**
 * CurrenciesController implements the CRUD actions for Currencies model.
 */
class CurrenciesController extends DefaultController
{
    public $modelClass = 'app\models\Currencies';
    public $modelSearchClass = 'app\models\search\CurrenciesSearch';
}
