<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\Logs;
use app\models\UserBalance;
use app\models\search\UserBalanceSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\httpclient\Client;
/**
 * UserBalanceController implements the CRUD actions for UserBalance model.
 */
class UserBalanceController extends DefaultController
{
    public $modelClass = 'app\models\UserBalance';

    public function actionIndex()
    {
        $searchModel = new UserBalanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserBalance model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserBalance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
    }

    public function actionTransferAmountValidate(){
        $model = new \app\models\UserBalance();
        $model->scenario = \app\models\UserBalance::SCENARIO_TRANSFER_AMOUNT;

        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionTransferAmount( $id )
    {
        $model = \app\models\UserBalance::find()->where(['id'=>$id])->one();

        $userBalanceIn = $model->balance_in;
        $userBalance = $model->user->balance;
        $userTariff = $model->user->tariff;
        $userId = $model->user_id;
        $transferedRecipetId = $model->receipt_id;
        $transferedTransaction = $model->transaction;

        $model->scenario = \app\models\UserBalance::SCENARIO_TRANSFER_AMOUNT;

        $token = \webvimark\modules\UserManagement\models\User::find()->where(['id'=>Yii::$app->user->id])->asArray()->one()['auth_key'];
  
        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            $userModel = \app\models\Users::find()->where(['id'=>$userId])->one();
            $transferedContract = Yii::$app->request->post('UserBalance')['contract_number'];
            $receiptModel = \app\models\Receipt::find()->where(['id'=>$transferedRecipetId])->one();
            $receiptCode = $receiptModel->code;

             $receiptModel->delete();
             $model->delete();

              $client = new Client([
                'transport' => 'yii\httpclient\CurlTransport'
              ]);
              $response = $client->createRequest()
              ->addHeaders([
                  'content-type' => 'application/json',
                  'Authorization' => 'Bearer '.$token,
              ])
              ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
              ->setUrl('localhost/api/add-balance')
              ->setMethod('POST')
              ->setData( 
                [
                'contract_number'=> $transferedContract,
                'balance_in'=>$userBalanceIn,
                'transaction'=> $transferedTransaction." (transfer)",
                'receipt'=>$receiptCode,
                ] 
              )
       
              ->send();



            $balanceOutModel = \app\models\UserBalance::find()
            ->where(['receipt_id'=>$transferedRecipetId])
            ->andWhere(['balance_in'=>0])
            ->all();
   

            if ( $balanceOutModel  > 0  ) {
                if ( $userBalance < $userTariff  ) {
                    $userModel = \app\models\Users::find()->where(['id'=>$userId])->one();
                    $userModel->status = 2;
                    $userModel->save(false);
                     foreach ( $balanceOutModel as $outBalanceKey => $outBalance ) {
                            if ( $outBalance->userServicePacket->service->service_alias == "internet" ) {
                           
                                \app\models\radius\Radgroupreply::block(  $outBalance->userServicePacket->usersInet->login );
                                \app\components\COA::disconnect(  $outBalance->userServicePacket->usersInet->login );

                                $inetModel = \app\models\UsersInet::find()->where(['u_s_p_i'=>$outBalance->userServicePacket->id])->one();
                                $inetModel->status = 2;
                                $inetModel->save(false);
         
                            }
                            if ( $outBalance->userServicePacket->service->service_alias == "tv" ) {
                                $tvModel = \app\models\UsersTv::find()->where(['u_s_p_i'=>$outBalance->userServicePacket->id])->one();
                                $tvModel->status = 2;
                                $tvModel->save(false);
                            }

                            if ( $outBalance->userServicePacket->service->service_alias == "wifi" ) {
                                $wifiModel = \app\models\UsersTv::find()->where(['u_s_p_i'=>$outBalance->userServicePacket->id])->one();
                                $wifiModel->status = 2;
                                $wifiModel->save(false);
                            }

                            $userServicePacketModel = \app\models\UsersServicesPackets::find()->where(['id'=>$outBalance->userServicePacket->id])->one();
                            $userServicePacketModel->status = 2;
                            $userServicePacketModel->save(false);

                       $deleteOldBalance = \app\models\UserBalance::find()->where(['id'=>$outBalance['id']])->one();
                       $deleteOldBalance->delete();
                    }
                   
                    $userModel->status = 2;
                }
                    $userModel->balance = \app\models\UserBalance::CalcUserTotalBalance( $userId );
                    $userModel->save(false);
            }
            
            
           return $this->redirect(['index']);
        }

        return $this->renderIsAjax('transfer-amount', [
            'model' => $this->findModel($id),
        ]);    
    }


    public function actionStatistc(){
        $today = \app\models\UserBalance::find()
        ->leftJoin('users','users.id=user_balance.user_id')
        ->orderBy(['created_at' => SORT_ASC])
        ->andWhere(['!=', 'user_balance.status', '1'])
        ->withByLocation()
        ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(user_balance.created_at), "%Y-%m-%d")' => date('Y-m-d')])
        ->asArray()
        ->all();

        $lastTransactions = \app\models\UserBalance::find()
        ->select('user_balance.*,users.fullname as fullname')
        ->leftJoin('users','users.id=user_balance.user_id')
        ->andWhere(['not', ['user_id' => null]])
        ->withByLocation()
        ->orderBy(['user_balance.id'=>SORT_DESC])
        ->limit(20)
        ->asArray()
        ->all();
    
        $lastTotalModel = \app\models\TotalProfit::find()
        ->orderBy(['created_at'=>SORT_DESC])
        ->asArray()
        ->one();

        $today_balance = 0;
        foreach ($today as $key => $t_v) {
           $today_balance += $t_v['balance_in'];
        }

        $all_data = [
            'today_balance' => $today_balance,
            'lastTotalModel' => $lastTotalModel,
            'lastTransactions'=>$lastTransactions,
        ];

        return $this->render('statistic', $all_data);

    }



    public function actionPaymentCalculator()
    {
        $model = new \app\models\UserBalance;
        $data = [];
        if (Yii::$app->request->get()) {
          if( Yii::$app->request->get('start_end_date') == "" ){
            return $this->renderIsAjax('statistic', [
                'model' => $model,
                'data' => $data,
            ]);
          }
            $s_e = explode("-", Yii::$app->request->get('start_end_date'));
            $start_date = trim($s_e[0]);
            $end_date = trim($s_e[1]);

            $payment_method = Yii::$app->request->get("payment_method");
            $query = \app\models\UserBalance::find()->select('user_balance.*,users.fullname as p_fullname,receipt.code as receipt')
                ->leftJoin('users', 'users.id=user_balance.user_id')
                ->leftJoin('receipt', 'receipt.id=user_balance.receipt_id')
                ->orderBy(['user_balance.created_at' => SORT_ASC])
                ->where(['and', ['>=', "DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), '%Y/%m/%d')", $start_date], ['<=', "DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), '%Y/%m/%d')", $end_date]])
                ->andWhere(['!=', 'balance_in', 0]);
            $data = ($payment_method != "" ? $query->andWhere(['payment_method'=>$payment_method])->asArray()->all() : $query->asArray()->all());
          }

        return $this->renderIsAjax('calculator', [
            'model' => $model,
            'data' => $data,
        ]);
    }


    public function actionUpdate($id){
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user_model = \app\models\Users::find()
            ->where(['id' => $model->user_id])
            ->withByLocation()
            ->one();
            $user_model->balance = \app\models\UserBalance::CalcUserTotalBalance($model->user_id);
            $user_model->bonus = \app\models\UserBalance::CalcUserTotalBonus($model->user_id);
            if ($user_model->save(false)) {
                $member_name = Yii::$app->user->username;
                $balance_in = Yii::$app->request->post('UserBalance')['balance_in'];
                $balance_out = Yii::$app->request->post('UserBalance')['balance_out'];
                $bonus_in = Yii::$app->request->post('UserBalance')['bonus_in'];
                $bonus_out = Yii::$app->request->post('UserBalance')['bonus_out'];
                $logMessage = "{$member_name} member (additional balance : {$balance_in} , additional bonus : {$bonus_in} ) ( out balance : {$balance_out} , out bonus : {$bonus_out} ) update from balance";
                    
                 Logs::writeLog(Yii::$app->user->username, intval($user_model->id), $logMessage, time());
                    
                
            }
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
        ]);
    }


    public function actionDelete($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $receipt_id = $model->receipt_id;
        if ($model->delete()) {
            $userModel = \app\models\Users::find()
            ->where(['id' => $model->user_id])
            ->withByLocation()
            ->one();

            $receiptModel = \app\models\Receipt::find()
            ->where(['id' => $receipt_id])
            ->one();
            if ($userModel != null) {
                $userModel->balance = \app\models\UserBalance::CalcUserTotalBalance($model->user_id);
                $userModel->bonus = \app\models\UserBalance::CalcUserTotalBonus($model->user_id);
                if ($userModel->save(false)) {

                    $memberUsername = Yii::$app->user->username;
                    $balanceIn = Yii::$app->request->get('balance_in');
                    $balanceOut = Yii::$app->request->get('balance_out');
                    $userName =Yii::$app->request->get('username');

                    $logMessage = "Balance in : {$balanceIn} balance out : {$balanceOut} was deleted from {$userName} payments by {$memberUsername}";
                    
                    if ($receiptModel != null) {
                        $receiptModel->status = '2';
                        $receiptModel->save(false);
                    }
                    Logs::writeLog(Yii::$app->user->username, intval($userModel->id), $logMessage, time());
                    return ['status' => 'success'];
                }
            }

        }

    }

    public function actionBulkDelete()
    {
        if (Yii::$app->request->post('selection')) {
            foreach (Yii::$app->request->post('selection', []) as $id) {
                $model = UserBalance::findOne($id);
                $balance_in = $model->balance_in;
                $balance_out = $model->balance_out;
                $receipt_id = $model->receipt_id;
                $user_id = $model->user_id;
                if ($model) {
                    if ($model->delete()) {
                        $user_model = \app\models\Users::find()
                        ->where(['id' => $user_id])
                        ->withByLocation()
                        ->one();
                        $receipt_model = \app\models\Receipt::find()
                        ->where(['id' => $receipt_id])
                        ->one();
                        if ($user_model != null) {
                            $user_model->balance = UserBalance::CalcUserTotalBalance($model->user_id);
                            if ($user_model->save(false)) {
                                $log_text = Yii::$app->user->username . " (additional balance " . $balance_in . "  )" . " (out balance " . $balance_out . "  )" . " deleted  from  " . $user_model->fullname . " balance";
                                Logs::writeLog(Yii::$app->user->username, intval($user_model->id), $log_text, time());
                            }
                        }
                        if ($receipt_model != null) {
                            $receipt_model->status = '2';
                            $receipt_model->save(false);
                        }
                    }
                }
            }
        }
    }

    /**
     * Finds the UserBalance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserBalance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserBalance::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
