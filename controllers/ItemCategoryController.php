<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * StoreCategoryController implements the CRUD actions for StoreCategory model.
 */
class ItemCategoryController extends DefaultController
{
    public $modelClass = 'app\models\ItemCategory';    
    public $modelSearchClass = 'app\models\search\ItemCategorySearch';   
     
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
