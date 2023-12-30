<?php

namespace webvimark\components;
use webvimark\modules\UserManagement\components\GhostAccessControl;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
	/**
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'ghost-access'=> [
				'class' => GhostAccessControl::className(),
			],
		];
	}

	/**
	 * Render ajax or usual depends on request
	 *
	 * @param string $view
	 * @param array $params
	 *
	 * @return string|\yii\web\Response
	 */
	protected function renderIsAjax($view, $params = [])
	{
		if ( Yii::$app->request->isAjax )
		{
			return $this->renderAjax($view, $params);
		}
		else
		{
			return $this->render($view, $params);
		}
	}

	public function beforeAction($action)
	{
		if ( parent::beforeAction($action) )
		{
	        $theme = "light";
	        if (Yii::$app->request->cookies['theme']) {
	            $theme = Yii::$app->request->cookies->getValue('theme');

	        }
	        Yii::$app->view->theme = new \yii\base\Theme([
	            'pathMap' => ['@app/views' => '@app/themes/'.$theme],
	            'baseUrl' => '@web',

	        ]);

			return true;
		}

		return false;

	}





}