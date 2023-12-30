<?php

namespace webvimark\modules\UserManagement\controllers;

use webvimark\components\AdminDefaultController;
use Yii;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\models\search\UserSearch;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AdminDefaultController
{
	/**
	 * @var User
	 */
	public $modelClass = 'webvimark\modules\UserManagement\models\User';

	/**
	 * @var UserSearch
	 */
	public $modelSearchClass = 'webvimark\modules\UserManagement\models\search\UserSearch';

	/**
	 * @return mixed|string|\yii\web\Response
	 */
	public function actionCreate(){
		$model = new User();
		$model->scenario = User::SCENARIO_CREATE;
		if ( $model->load(Yii::$app->request->post()) && $model->validate() ){
        		$model->photo_file = \yii\web\UploadedFile::getInstance($model, 'photo_file');
                if ($model->photo_file) {
                    $imageName = Yii::$app->security->generateRandomString(6);
                    $file = Yii::getAlias('@webroot').'/uploads/users/profile/'.$imageName.'.'.$model->photo_file->extension;
                    $model->photo_file->saveAs($file);
                    $model->photo_url = $imageName.'.'.$model->photo_file->extension;
                }
			if ($model->save()) {
				if (Yii::$app->request->post('User')['city_id'] != null || Yii::$app->request->post('User')['district_id'] != null || Yii::$app->request->post('User')['location_id'] != null) {
						$MemberLocation = new \app\models\MemberLocation;
						$MemberLocation->member_id = $model->id;
						if (Yii::$app->request->post('User')['city_id'] != null) {
							$cities = implode(",", Yii::$app->request->post('User')['city_id']);
							$MemberLocation->city_id = $cities;
						}
						if (Yii::$app->request->post('User')['district_id'] != null) {
							$districts = implode(",", Yii::$app->request->post('User')['district_id']);
							$MemberLocation->district_id = $districts;
						}
						if (Yii::$app->request->post('User')['location_id'] != null) {
							$locations = implode(",", Yii::$app->request->post('User')['location_id']);
							$MemberLocation->location_id = $locations;
						}
						$MemberLocation->save(false);
				}
			}
			return $this->redirect(['index']);
		}
		return $this->renderIsAjax('create', compact('model'));
	}


	public function actionPermissonForMember($id){
		$check = \app\models\MemberLocation::find()->where(['member_id'=>$id])->one();

		if ($check != null) {

			$model = \app\models\MemberLocation::find()->where(['member_id'=>$id])->one();
			if (Yii::$app->request->post() && $model->validate(false)) {
				\app\models\MemberLocation::deleteAll(['member_id'=>$id]);
				$MemberLocation = new \app\models\MemberLocation;
				if (Yii::$app->request->post('MemberLocation')['city_id'] != null) {
					$MemberLocation->member_id = $id;
					$cities = implode(",", Yii::$app->request->post('MemberLocation')['city_id']);
					$MemberLocation->city_id = $cities;
				}

				if (Yii::$app->request->post('MemberLocation')['district_id'] != null) {
					$districts = implode(",", Yii::$app->request->post('MemberLocation')['district_id']);
					$MemberLocation->district_id = $districts;

				}
					$MemberLocation->save(false);

				return $this->redirect(['index']);

			}		
		}else{
			$model = new \app\models\MemberLocation;
			if (Yii::$app->request->post() && $model->validate(false)) {
				\app\models\MemberLocation::deleteAll(['member_id'=>$id]);
				$MemberLocation = new \app\models\MemberLocation;
				if (Yii::$app->request->post('MemberLocation')['city_id'] != null) {
					$MemberLocation->member_id = $id;
					$cities = implode(",", Yii::$app->request->post('MemberLocation')['city_id']);
					$MemberLocation->city_id = $cities;
				}

				if (Yii::$app->request->post('MemberLocation')['district_id'] != null) {
					$districts = implode(",", Yii::$app->request->post('MemberLocation')['district_id']);
					$MemberLocation->district_id = $districts;
				}
					$MemberLocation->save(false);

				return $this->redirect(['index']);

			}
		}


		return $this->renderIsAjax('permissonForMember', compact('model','id'));
	}



	public function actionUpdate($id){
		$model = $this->findModel($id);
		$member_location = \app\models\MemberLocation::find()->where(['member_id'=>$id])->one();

		if ( $model->load(Yii::$app->request->post()) AND $model->validate()){
            if ($model->photo_file) {
                $imageName = Yii::$app->security->generateRandomString(6);
                $file = Yii::getAlias('@webroot').'/uploads/users/profile/'.$imageName.'.'.$model->photo_file->extension;
                $model->photo_file->saveAs($file);
                $model->photo_url = $imageName.'.'.$model->photo_file->extension;
            }
            if ($model->save()) {
            	\app\models\MemberLocation::deleteAll(['member_id'=>$id]);
				if (Yii::$app->request->post('User')['city_id'] != null || Yii::$app->request->post('User')['district_id'] != null || Yii::$app->request->post('User')['location_id'] != null) {
						$MemberLocation = new \app\models\MemberLocation;
						$MemberLocation->member_id = $model->id;
						if (Yii::$app->request->post('User')['city_id'] != null) {
							$cities = implode(",", Yii::$app->request->post('User')['city_id']);
							$MemberLocation->city_id = $cities;
						}
						if (Yii::$app->request->post('User')['district_id'] != null) {
							$districts = implode(",", Yii::$app->request->post('User')['district_id']);
							$MemberLocation->district_id = $districts;
						}
						if (Yii::$app->request->post('User')['location_id'] != null) {
							$locations = implode(",", Yii::$app->request->post('User')['location_id']);
							$MemberLocation->location_id = $locations;
						}
						$MemberLocation->save(false);
				}
            }
			return $this->redirect(['index']);
		}

		return $this->renderIsAjax('update', compact('model','member_location'));
	}



	/**
	 * @param int $id User ID
	 *
	 * @throws \yii\web\NotFoundHttpException
	 * @return string
	 */
	public function actionChangePassword($id)
	{
		$model = User::findOne($id);

		if ( !$model )
		{
			throw new NotFoundHttpException('User not found');
		}

		$model->scenario = User::SCENARIO_CHANGE_PASSWORD;

		if ( $model->load(Yii::$app->request->post()) AND $model->validate() )
		{
			$model->save();
	
			$model->save(false);
			return $this->redirect(['index']);
		}

		return $this->renderIsAjax('changePassword', compact('model'));
	}

	public function actionToggleAttribute($attribute, $id)
	{
		$model = $this->findModel($id);
		$model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
		$model->save(false);
		return $this->redirect('index');
	}


}
