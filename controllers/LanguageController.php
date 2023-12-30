<?php

namespace app\controllers;

use Yii;
use app\models\Language;
use app\models\search\LanguageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \app\components\DefaultController;
use yii\data\ActiveDataProvider;
class LanguageController extends DefaultController
{
    public $modelClass = 'app\models\Language';


public function actionIndex()
    {
        if ($this->modelSearchClass) {
            $searchModel = new $this->modelSearchClass;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $searchModel = null;
             $dataProvider = new ActiveDataProvider([
                'query' => \app\models\Language::find(),
                'pagination' => [
                    'pageSize' => \Yii::$app->request->cookies->getValue( '_grid_page_size_lang', 20),
                ]
            ]);
        }     


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }




    public function actionCreate(){

             $save = false;
        $model = new $this->modelClass;


        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        if ( $model->load(Yii::$app->request->post()) AND $model->validate())
        {
            
            try {
                $save = $model->save();
 
                ($save) ? $transaction->commit() : $transaction->rollBack();
            } catch (Exception $e) {
                $transaction->rollBack();
            }
            if ($save) {
                return $this->redirect(['index']);
            }
        }


        return $this->renderIsAjax("create", compact('model'));
    }


    public function actionFileEdit($id){
        $model = \app\models\Language::findOne($id);
        if (Yii::$app->request->isPost) {
            file_put_contents(  Yii::getAlias("@app").'/messages/'.$model->alias.'/app.php',   "<?php\nreturn " . var_export(Yii::$app->request->post()["lang"], true) . "\n?>");
             return $this->redirect(['index']);
        }
        return $this->render('file_edit',['model'=>$model]);
    }



    public function actionBulkDelete()
    {
        if ( Yii::$app->request->post('selection') )
        {
            $modelClass = $this->modelClass;

            foreach (Yii::$app->request->post('selection', []) as $id)
            {
                $model = \app\models\Language::findOne($id);

                if ( $model )
                    $model->delete();
            }
        }
    }




    public function actionGridSort()
    {
        if ( Yii::$app->request->post('sorter') )
        {
            $sortArray = Yii::$app->request->post('sorter',[]);

            $modelClass = $this->modelClass;

            $models = \app\models\Language::findAll(array_keys($sortArray));

            foreach ($models as $model)
            {
                $model->sorter = $sortArray[$model->id];
                $model->save(false);
            }

        }
    }



    protected function findModel($id){
        $modelClass = $this->modelClass;

        if ( ($model = \app\models\Language::findOne($id)) !== null )
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }


}
