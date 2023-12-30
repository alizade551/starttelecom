<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\Locations;
use app\models\search\LocationSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LocationsController implements the CRUD actions for Locations model.
 */
class LocationsController extends DefaultController
{
    public function actionIndex()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a multiple user's cordinates .
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMap($id)
    {
        $location = \app\models\Locations::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();

        $siteConfig = \app\models\SiteConfig::find()->one();


        if ( $location != null ) {
            $boxexData = [];
            $usersData = [];
            $devicesData = [];
            $routersData = [];

            $boxes = \app\models\EgponBox::find()
            ->select('egpon_box.cordinate as cordinate,egpon_box.box_name as box_name,devices.name as device_name')
            ->leftJoin('devices','devices.id=egpon_box.device_id')
            ->where(['not', ['egpon_box.cordinate' => null]])
            ->andWhere(['egpon_box.location_id'=>$id])
            ->asArray()
            ->all();

            $devices = \app\models\Devices::find()
            ->select('devices.cordinate as cordinate,devices.name as device_name,devices.type as device_type')
            ->where(['not', ['cordinate' => null]])
            ->andWhere(['location_id'=>$id])
            ->asArray()
            ->all();


            $users = \app\models\Users::find()
            ->select('users.cordinate as cordinate,users.fullname as user_fullname,users.status as status,users.id as id')
            ->where(['location_id'=>$id])
            ->andWhere(['not', ['cordinate' => null]])
            ->andWhere(['!=', 'status', '0'])
            ->asArray()
            ->all();


            $routers = \app\models\radius\Nas::find()
            ->select('nas.cordinate as cordinate,nas.nasname  as router_name,nas.vendor_name as router_vendor_name')
            ->where(['location_id'=>$id])
            ->andWhere(['not', ['cordinate' => null]])
            ->asArray()
            ->all();


            foreach ($routers as $rkey => $router) {
                $routersData[$rkey]['icon'] = '/img/router_map.svg';
                $routersData[$rkey]['type'] = 'router';
                $routersData[$rkey]['name'] = $router['router_name']." - ".$router['router_vendor_name'];
                $routersData[$rkey]['longitude'] = floatval( explode(  ',' , $router['cordinate'] )[0] ) ;
                $routersData[$rkey]['latitude'] = floatval( explode(  ',' , $router['cordinate'] )[1] ) ;
            }

            foreach ($boxes as $bkey => $box) {
                $boxexData[$bkey]['icon'] = '/img/map_box.svg';
                $boxexData[$bkey]['type'] = 'box';
                $boxexData[$bkey]['name'] = $box['device_name']." - ".$box['box_name'];
                $boxexData[$bkey]['longitude'] = floatval( explode(  ',' , $box['cordinate'] )[0] ) ;
                $boxexData[$bkey]['latitude'] = floatval( explode(  ',' , $box['cordinate'] )[1] ) ;
            }

            foreach ($users as $ukey => $user) {
                $usersData[$ukey]['type'] = 'user';
                if ( $user['status'] == 2 ) {
                    $usersData[$ukey]['icon'] = '/img/user_deactive_map.svg';
                }elseif ( $user['status'] == 1 ) {
                    $usersData[$ukey]['icon'] = '/img/user_active_map.svg';
                }elseif ( $user['status'] == 3 ) {
                    $usersData[$ukey]['icon'] = '/img/user_archive_map.svg';
                }elseif ( $user['status'] == 7 ) {
                    $usersData[$ukey]['icon'] = '/img/user_vip_map.svg';
                }

                $usersData[$ukey]['name'] = $user['user_fullname'];
                $usersData[$ukey]['id'] = $user['id'];
                $usersData[$ukey]['longitude'] = floatval( explode(  ',' , $user['cordinate'] )[0] ) ;
                $usersData[$ukey]['latitude'] = floatval( explode(  ',' , $user['cordinate'] )[1] ) ;
            }

            foreach ($devices as $dkey => $device) {
                $devicesData[$dkey]['type'] = 'device';
                if ( $device['device_type'] != "switch" ) {
                    $devicesData[$dkey]['icon'] = '/img/device_map.svg';
                }else{
                     $devicesData[$dkey]['icon'] = '/img/switch_map.svg';
                }
                $devicesData[$dkey]['name'] = $device['device_name'];
                $devicesData[$dkey]['longitude'] = floatval( explode(  ',' , $device['cordinate'] )[0] ) ;
                $devicesData[$dkey]['latitude'] = floatval( explode(  ',' , $device['cordinate'] )[1] ) ;
            }
            $data = yii\helpers\Arrayhelper::merge($boxexData,$usersData,$devicesData,$routersData);
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->renderIsAjax('map', [
            'data' => $data,
            'location' => $location,
            'siteConfig' => $siteConfig

        ]);
    }


    /**
     * Displays a single Locations model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        die;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Locations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Locations();
        $siteConfig = \app\models\SiteConfig::find()->one();

        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $request->isAjax && $model->load($request->post()) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }

    /**
     * Updates an existing Locations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $siteConfig = \app\models\SiteConfig::find()->one();

        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $request->isAjax && $model->load($request->post()) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }

    /**
     * Deletes an existing Locations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Locations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Locations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Locations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
