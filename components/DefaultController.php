<?php
namespace app\components;


use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use Yii;
use app\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * @var ActiveRecord
	 */
	public $modelClass;

	/**
	 * @var ActiveRecord
	 */
	public $modelLangClass;

	/**
	 * @var ActiveRecord
	 */
	public $modelSearchClass;

	/**
	 * @var string
	 */
	public $scenarioOnCreate;

	/**
	 * @var string
	 */
	public $scenarioOnUpdate;

	/**
	 * Actions that will be disabled
	 *
	 * List of available actions:
	 *
	 * ['index', 'view', 'create', 'update', 'delete', 'toggle-attribute',
	 * 'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size']
	 *
	 * @var array
	 */
	public $disabledActions = [];

	/**
	 * Opposite to $disabledActions. Every action from AdminDefaultController except those will be disabled
	 *
	 * But if action listed both in $disabledActions and $enableOnlyActions
	 * then it will be disabled
	 *
	 * @var array
	 */
	public $enableOnlyActions = [];

	/**
	 * List of actions in this controller. Needed fo $enableOnlyActions
	 *
	 * @var array
	 */
	protected $_implementedActions = ['index', 'view', 'create', 'update', 'delete', 'toggle-attribute',
		'bulk-activate', 'bulk-deactivate', 'bulk-delete', 'grid-sort', 'grid-page-size'];


	public $createClass = 'create';

	public $updateClass = 'update';

	public $redirectPage = 'index';

	public $_id = false;



    public function actionIndex()
    {
    	if ($this->modelSearchClass) {
    		$searchModel = new $this->modelSearchClass;
        	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	}else{
    		$searchModel = null;
    		 $dataProvider = new ActiveDataProvider([
	            'query' => $this->modelClass::find(),
	        ]);
    	}     

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



	/**
	 * Updates an existing model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionCreate()
	{


		$mid = $this->_id;
	
		if ($this->modelLangClass) {

			return $this->createWithLang();
		}
		$model = new $this->modelClass;


		if ( $this->scenarioOnCreate )
		{
			$model->scenario = $this->scenarioOnCreate;
		}

		if ( $model->load(Yii::$app->request->post()) && $model->save())
		{
			$redirect = $this->getRedirectPage($this->redirectPage,$this->_id);

			return $redirect === false ? '' : $this->redirect($redirect);
		}

		
		return $this->renderIsAjax($this->createClass, compact('model','mid'));
		
	}


		/**
	 * Updates an existing model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$mid = $this->_id;
		if ($this->modelLangClass) {


			return $this->updateWithLang($id);
		}
		$model = $this->findModel($id);

		if ( $this->scenarioOnUpdate )
		{
			$model->scenario = $this->scenarioOnUpdate;
		}

		if ( $model->load(Yii::$app->request->post()) AND $model->save())
		{
			$redirect = $this->getRedirectPage($this->redirectPage,$this->_id);

			return $redirect === false ? '' : $this->redirect($redirect);
		}

		return $this->renderIsAjax($this->updateClass, compact('model','mid'));
	}




	/**
	 * Deletes an existing model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel($id);
		$model->delete();

		$redirect = $this->getRedirectPage('delete', $model);

		return $redirect === false ? '' : $this->redirect($redirect);
	}
	/**
	 * @param string $attribute
	 * @param int $id
	 */
	public function actionToggleAttribute($attribute, $id)
	{
		$model = $this->findModel($id);
		$model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
		$model->save(false);
		return $this->redirect('index');
	}


	/**
	 * Activate all selected grid items
	 */
	public function actionBulkStatus($attribute,$value)
	{
		if ( Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::updateAll(
				[$attribute=>$value],
				['id'=>Yii::$app->request->post('selection', [])]
			);
		}
	}

	/**
	 * Activate all selected grid items
	 */
	public function actionBulkActivate($attribute = 'active')
	{
		if ( Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::updateAll(
				[$attribute=>1],
				['id'=>Yii::$app->request->post('selection', [])]
			);
		}
	}


	/**
	 * Deactivate all selected grid items
	 */
	public function actionBulkDeactivate($attribute = 'active')
	{
		if ( Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::updateAll(
				[$attribute=>0],
				['id'=>Yii::$app->request->post('selection', [])]
			);
		}
	}

	/**
	 * Deactivate all selected grid items
	 */
	public function actionBulkDelete()
	{
		if ( Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			foreach (Yii::$app->request->post('selection', []) as $id)
			{
				$model = $modelClass::findOne($id);

				if ( $model )
					$model->delete();
			}
		}
	}


	/**
	 * Sorting items in grid
	 */
	public function actionGridSort()
	{
		if ( Yii::$app->request->post('sorter') )
		{
			$sortArray = Yii::$app->request->post('sorter',[]);

			$modelClass = $this->modelClass;

			$models = $modelClass::findAll(array_keys($sortArray));

			foreach ($models as $model)
			{
				$model->sorter = $sortArray[$model->id];
				$model->save(false);
			}

		}
	}


	/**
	 * Set page size for grid
	 */
	public function actionGridPageSize()
	{
		if ( Yii::$app->request->post() )
		{
	
			$cookie = new Cookie([
				'name' => Yii::$app->request->post()['name'],
				'value' => Yii::$app->request->post('grid-page-size'),
				'expire' => time() + 86400 * 365, // 1 year
			]);

			Yii::$app->response->cookies->add($cookie);
		}
	}

	/**
	 * Finds the model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param mixed $id
	 *
	 * @return ActiveRecord the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		$modelClass = $this->modelClass;

		if ( ($model = $modelClass::findOne($id)) !== null )
		{
			return $model;
		}
		else
		{
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
	}




	/**
	 * Define redirect page after update, create, delete, etc
	 *
	 * @param string       $action
	 * @param ActiveRecord $model
	 *
	 * @return string|array
	 */
	protected function getRedirectPage($action, $id = false)
	{

		if ($id) {
			return Yii::$app->request->isAjax ? false : [$this->redirectPage, 'id'=>$id];
		}else{
			return Yii::$app->request->isAjax ? false : $this->redirectPage;
		}
	
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action){

        if ($action->id == 'grid-view-visibility') {
            $this->enableCsrfValidation = false;
        }

		if ( parent::beforeAction($action) )
		{
			$currencyCount = \app\models\Currencies::find()
			->asArray()
			->count();

			$messageLangCount = \app\models\MessageLang::find()
			->asArray()
			->count();

			if (!Yii::$app->user->isGuest) {
	            $user_model = \webvimark\modules\UserManagement\models\User::find()->where(['id' => Yii::$app->user->id])->one();
	            $theme = $user_model->default_theme;
	            Yii::$app->view->theme = new \yii\base\Theme([
	                'pathMap' => ['@app/views' => '@app/themes/'.$theme],
	                'baseUrl' => '@web',

	            ]);
			}	
			// $this->layout = "blocked";
			if ( $this->enableOnlyActions !== [] AND in_array($action->id, $this->_implementedActions) AND !in_array($action->id, $this->enableOnlyActions) )
			{
				throw new NotFoundHttpException('Page not found');
			}

			if ( in_array($action->id, $this->disabledActions) )
			{
				throw new NotFoundHttpException('Page not found');
			}
	
			if ( $currencyCount == 0 &&  Yii::$app->controller->id != "currencies" ) {
				 return Yii::$app->response->redirect(['currencies/index']);
			}

			if ( $messageLangCount == 0 &&  Yii::$app->controller->id != "message-lnag" ) {
				 return Yii::$app->response->redirect(['message-lnag/index']);
			}


			return true;
		}

		return false;

	}


    public function actionGridViewVisibility(){
        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            $cookies = Yii::$app->response->cookies;
            // add a new cookie to the response to be sent
            $cookies->add(new \yii\web\Cookie([
                'name' => Yii::$app->controller->id.'GridViewVisibility',
                'value' => json_encode( Yii::$app->request->post() ),
            ]));
            return $this->redirect(['index']);
        }
    }

}