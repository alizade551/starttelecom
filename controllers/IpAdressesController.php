<?php

namespace app\controllers;

use Yii;
use app\models\IpAdresses;
use app\models\search\IpAdressesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;
use app\components\RouterosApi;


/**
 * IpAdressesController implements the CRUD actions for IpAdresses model.
 */
class IpAdressesController extends DefaultController
{
    public $modelClass = 'app\models\IpAdresses';
    public $modelSearchClass = 'app\models\search\IpAdressesSearch';



    public function actionCreateValidate()
    {
        $model = new IpAdresses();
        $model->scenario = IpAdresses::SCENARIO_CREATE;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionCreate(){
        $model = new IpAdresses();
        $model->scenario = IpAdresses::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }

            $start_arr = explode('.',Yii::$app->request->post('IpAdresses')['start_ip']);
            $end_arr =  explode('.',Yii::$app->request->post('IpAdresses')['end_ip']);
       
           

            if ( Yii::$app->request->post('IpAdresses')['type'] == "0" ) {
                $splitCount =  intval(Yii::$app->request->post('IpAdresses')['split']);
                $portsPair = \app\components\Utils::ipPortSplitting( intval( $splitCount ) );
                $cgnIpAddressModelExsist = \app\models\CgnIpAddress::find()
                ->orderBy(['id'=>SORT_DESC])
                ->asArray()
                ->one();
                if ( $cgnIpAddressModelExsist != null ) {
                   $start_ip = ip2long($cgnIpAddressModelExsist['internal_ip']) + 1;
                }else{
                    $start_ip = ip2long('100.64.64.1');
                }
            }

            $check = $model->checkIp( ip2long( Yii::$app->request->post('IpAdresses')['start_ip'] ),ip2long( Yii::$app->request->post('IpAdresses')['end_ip'] ) );

            if ( $check == true) {
                while($start_arr <= $end_arr){
                    $ip = implode('.',$start_arr);
                    $IpAdressesModel = new \app\models\IpAdresses;
                    $IpAdressesModel->public_ip = $ip;
                    $IpAdressesModel->router_id = Yii::$app->request->post('IpAdresses')['router_id'];
                    $IpAdressesModel->type = Yii::$app->request->post('IpAdresses')['type'];
                    $IpAdressesModel->created_at = time();
                    if ( Yii::$app->request->post('IpAdresses')['type'] == "0" ) {
                        $IpAdressesModel->split = Yii::$app->request->post('IpAdresses')['split'];
                    }
                    if ( $IpAdressesModel->save(false) ) {
                        if ( Yii::$app->request->post('IpAdresses')['type'] == "0" ) {
                            foreach ($portsPair as $portk => $port) {
                             $cgnIpAddressModel = new \app\models\CgnIpAddress;
                                $cgnIpAddressModel->ip_address_id = $IpAdressesModel->id;
                                $cgnIpAddressModel->port_range = $port;
                                $cgnIpAddressModel->internal_ip = long2ip($start_ip);
                                if ($cgnIpAddressModel->save(false)) {
                                    //    \app\components\MikrotikQueries::addCgnIp(
                                    //     $IpAdressesModel->public_ip,
                                    //     $IpAdressesModel->router->interface,
                                    //     long2ip( $start_ip ),
                                    //     $port,
                                    //     $IpAdressesModel->router->nas,
                                    //     $IpAdressesModel->router->username,
                                    //     $IpAdressesModel->router->password,
                                    //     "addCgnIp",
                                    //     [   
                                    //         'public_ip'=>$IpAdressesModel->public_ip,
                                    //         'out_interface'=>$IpAdressesModel->router->interface,
                                    //         'internal_ip'=>long2ip($start_ip),
                                    //         'port_range'=>$port,
                                    //         'nas'=> $IpAdressesModel->router->nas,
                                    //         'router_username'=>$IpAdressesModel->router->username,
                                    //         'router_password'=> $IpAdressesModel->router->password
                                    //     ]
                                    // );
                                }
                                $start_ip++;
                            }
                        }
                    }

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
                            'start_ip'=>Yii::$app->request->post('IpAdresses')['start_ip'],
                            'end_ip'=>Yii::$app->request->post('IpAdresses')['end_ip'],
                        ]
                    ),
                    'url' => \yii\helpers\Url::to(['ip-adresses/index'], true)
                ];
            }else{
                return [
                    'status' => 'error',
                    'message'=>Yii::t('app','Theese ip has beed added before.Please use another ip range'),
                    
                ];
            }

        }

        return $this->renderIsAjax('create', [
            'model' => $model,
        ]);
    }





   public function actionDelete($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        
        if ( $model->type == "0" ) {
            $cgnIpAddressModel = \app\models\CgnIpAddress::find()
            ->select('cgn_ip_address.*,routers.interface as router_interface,routers.nas as router_nas,routers.username as router_username,routers.password as router_password')
            ->leftJoin('ip_adresses','ip_adresses.id=cgn_ip_address.ip_address_id')
            ->leftJoin('routers','routers.id=ip_adresses.router_id')
            ->where([ 'ip_address_id'=>$model->id ])
            ->asArray()
            ->all();

            foreach ($cgnIpAddressModel as $key => $cgnIp ) {
                \app\components\MikrotikQueries::removeCgnIp(
                    $cgnIp['internal_ip'],
                    $cgnIp['router_nas'],
                    $cgnIp['router_username'],
                    $cgnIp['router_password'],
                    "removeCgnIp",
                    [
                        "internal_ip"=> $cgnIp['internal_ip'],
                        "nas"=>$cgnIp['router_nas'],
                        "router_username"=>$cgnIp['router_username'],
                        "router_password"=>$cgnIp['router_password']
                    ],

                );      
            }

        }

        if ( $model->delete() ) {
            return ['status' => 'success'];
        }

    }



   public function actionCgnIp($id){
        $model = \app\models\CgnIpAddress::find()
        ->select('cgn_ip_address.*,ip_adresses.public_ip as public_ip')
        ->leftJoin('ip_adresses','ip_adresses.id=cgn_ip_address.ip_address_id ')
        ->where(['ip_address_id'=>$id])
        ->asArray()
        ->all();

        return $this->renderIsAjax('cgn-ip', [
            'model' => $model,
        ]);

   }


}
