<?php

namespace app\controllers;

use Yii;
use app\models\CgnIpAddress;
use app\models\search\CgnIpAddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * CgnIpAddressController implements the CRUD actions for CgnIpAddress model.
 */
class CgnIpAddressController extends DefaultController
{

    public $modelClass = 'app\models\CgnIpAddress';
    public $modelSearchClass = 'app\models\search\CgnIpAddressSearch';




    public function actionDefineRouter(){
        $model = new CgnIpAddress();
        $model->scenario = CgnIpAddress::SCENARIO_DEFINE_NATS_ROUTER;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }

            $start_arr = explode('.',Yii::$app->request->post('CgnIpAddress')['start_ip']);
            $end_arr =  explode('.',Yii::$app->request->post('CgnIpAddress')['end_ip']);
       

            $check = $model->checkIp( ip2long( Yii::$app->request->post('CgnIpAddress')['start_ip'] ),ip2long( Yii::$app->request->post('CgnIpAddress')['end_ip'] ) );



            if ( $check == true) {
                while($start_arr <= $end_arr){
                    $ip = implode('.',$start_arr);
                    $cgnIpAddress = \app\models\CgnIpAddress::find()->where(['internal_ip'=>$ip])->one();
                    $cgnIpAddress->router_id = Yii::$app->request->post('CgnIpAddress')['router_id'];
                    $cgnIpAddress->save( false );

                    $start_arr[3]++;
                    if($start_arr[3] == 256)
                    {
                        $start_arr[3] = 0;
                        $start_arr[2]++;
                        if($start_arr[2] == 256)
                        {
                            $start_arr[2] = 0;
                            $start_arr[1]++;
                            if($start_arr[1] == 256)
                            {
                                $start_arr[1] = 0;
                                $start_arr[0]++;
                            }
                        }
                    }
                }
                return [
                    'status' => 'success',
                    'message'=>Yii::t(
                    'app',
                    '{start_ip} - {end_ip} ip range added was successfuly',
                        [
                            'start_ip'=>Yii::$app->request->post('CgnIpAddress')['start_ip'],
                            'end_ip'=>Yii::$app->request->post('CgnIpAddress')['end_ip'],
                        ]
                    ),
                    'url' => \yii\helpers\Url::to(['cgn-ip-address/index'], true)
                ];
            }else{
                return [
                    'status' => 'error',
                    'message'=>Yii::t('app','Theese ip has beed defined before.Please use another ip range'),
                    
                ];
            }

        }

        return $this->renderIsAjax('define-router', [
            'model' => $model,
        ]);
    }


    public function actionDeleteNatFromRouter(){
        $model = new CgnIpAddress();
        $model->scenario = CgnIpAddress::SCENARIO_CLEAR_NATS_ROUTER;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }

            $start_arr = explode('.',Yii::$app->request->post('CgnIpAddress')['start_ip']);
            $end_arr =  explode('.',Yii::$app->request->post('CgnIpAddress')['end_ip']);
       
            $check = $model->checkIpExsistRouter( ip2long( Yii::$app->request->post('CgnIpAddress')['start_ip'] ),ip2long( Yii::$app->request->post('CgnIpAddress')['end_ip'] ) );

            if ( $check == true) {
                while($start_arr <= $end_arr){
                    $ip = implode('.',$start_arr);
                    $cgnIpAddress = \app\models\CgnIpAddress::find()->where(['internal_ip'=>$ip])->one();
                    $cgnIpAddress->router_id = null;
                    $cgnIpAddress->save( false );

                    $start_arr[3]++;
                    if($start_arr[3] == 256)
                    {
                        $start_arr[3] = 0;
                        $start_arr[2]++;
                        if($start_arr[2] == 256)
                        {
                            $start_arr[2] = 0;
                            $start_arr[1]++;
                            if($start_arr[1] == 256)
                            {
                                $start_arr[1] = 0;
                                $start_arr[0]++;
                            }
                        }
                    }
                }

                return [
                    'status' => 'success',
                    'message'=>Yii::t(
                    'app',
                    '{start_ip} - {end_ip} ip range added was successfuly',
                        [
                            'start_ip'=>Yii::$app->request->post('CgnIpAddress')['start_ip'],
                            'end_ip'=>Yii::$app->request->post('CgnIpAddress')['end_ip'],
                        ]
                    ),
                    'url' => \yii\helpers\Url::to(['cgn-ip-address/index'], true)
                ];
            }else{
                return [
                    'status' => 'error',
                    'message'=>Yii::t('app','Theese ip has beed defined before.Please use another ip range'),
                    
                ];
            }

        }

        return $this->renderIsAjax('delete-nat-from-router', [
            'model' => $model,
        ]);
    }


    
}
