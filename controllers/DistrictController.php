<?php

namespace app\controllers;

use app\models\District;
use app\models\search\DistrictSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\DefaultController;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class DistrictController extends DefaultController
{

    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DistrictSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $siteConfig = \app\models\SiteConfig::find()->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'siteConfig' => $siteConfig,
        ]);
    }

    public function actionCreate(){
        $model = new District();
        $siteConfig = \app\models\SiteConfig::find()->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig' => $siteConfig,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $siteConfig = \app\models\SiteConfig::find()->one();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $users = \app\models\Users::find()
            ->where(['district_id'=>$model->id])
            ->asArray()
            ->all();

            foreach ($users as $userKey => $user) {
              $userModel =  \app\models\Users::find()
               ->where(['id'=>$user['id']])
               ->one();
               $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($user['id'])['per_total_tariff'];
               $userModel->save(false);
            }
     
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDrawCoverage($id)
    {
        $model = $this->findModel($id);
        $siteConfig = \app\models\SiteConfig::find()->one();


        if (Yii::$app->request->isAjax && Yii::$app->request->isPost  ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
     
            $model->polygon_cord = serialize( Yii::$app->request->post('line'));
            $model->save(false);
            
        }

        return $this->renderIsAjax('draw-covarge', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }





    public function actionAddRouter($id)
    {
        $model = $this->findModel($id);
        $model->scenario = District::SCENARIO_ADD_ROUTER;
        $oldRouter = ( isset( $model->router->name ) == false )? false : $model->router->name;
 
        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $message =  ( $oldRouter == false ) ?   Yii::t('app','{new_router} router was defined',['new_router'=> $model->nas->nasname]) : Yii::t('app','{old_router} router was changed to {new_router}',['old_router'=> $oldRouter ,'new_router'=> $model->nas->nasname]);

            if ( $model->save(false) ) {
                return [
                    'status'=>'success',
                    'message'=>$message
                ];
            }
        }

        return $this->renderIsAjax('router', [
            'model' => $model,
        ]);
    }



    public function actionMap($id)
    {
        $district = \app\models\District::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();



        if ( $district != null ) {

            $boxexData = [];
            $usersData = [];
            $devicesData = [];
            $routersData = [];

            $boxes = \app\models\EgponBox::find()
            ->select('egpon_box.cordinate as cordinate,egpon_box.box_name as box_name,devices.name as device_name')
            ->leftJoin('devices','devices.id=egpon_box.device_id')
            ->where(['not', ['egpon_box.cordinate' => null]])
            ->andWhere(['egpon_box.district_id'=>$id])
            ->asArray()
            ->all();

            $devices = \app\models\Devices::find()
            ->select('devices.cordinate as cordinate,devices.name as device_name,devices.type as device_type')
            ->where(['not', ['cordinate' => null]])
            ->andWhere(['district_id'=>$id])
            ->asArray()
            ->all();

            $users = \app\models\Users::find()
            ->select('users.cordinate as cordinate,users.fullname as user_fullname,users.status as status')
            ->where(['district_id'=>$id])
            ->andWhere(['not', ['cordinate' => null]])
            ->andWhere(['!=', 'status', '0'])
            ->asArray()
            ->all();


            $routers = \app\models\Routers::find()
            ->select('routers.cordinate as cordinate,routers.name as router_name,routers.vendor_name as router_vendor_name')
            ->where(['district_id'=>$id])
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
            'district' => $district,
        ]);
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = District::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
