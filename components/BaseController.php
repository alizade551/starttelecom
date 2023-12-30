<?php

namespace app\components;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;



class BaseController extends Controller
{


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

	public function behaviors()
	{
		return [
			'ghost-access'=> [
				'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
			],
		];
	}
}