<?php

namespace app\controllers;

use app\components\DefaultController;
use Yii;
use yii\imagine\Image;
use yii\web\Response;
use yii\data\Pagination;

class SiteController extends DefaultController{

    public function lng($url = false){
        if (Yii::$app->language == "az") {
            $url = ($url) ? '/' : '';
        }else{
            $url = '/'.Yii::$app->language;
        }
        return $url;
    }

    public function actions()
    {
        return [
            'error' => [
                'layout' => 'error',
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function beforeAction($action)
    {            
        if ($action->id == 'config') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }



    public function actionConfig(){

        $model = \app\models\SiteConfig::find()->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->logo_photo = \yii\web\UploadedFile::getInstance($model, 'logo_photo');

            if ($model->logo_photo != "") {
                if (file_exists(Yii::getAlias('@app').'/web/uploads/logo/'.$model->logo) && $model->logo != null ) {
                    unlink(Yii::getAlias('@app').'/web/uploads/logo/'.$model->logo);
                }
            }  

            if ($model->logo_photo) {
                $imageName = Yii::$app->security->generateRandomString(20);
                $file = Yii::getAlias("@app").'/web/uploads/logo/'.$imageName.'.'.$model->logo_photo->extension;
                $model->logo_photo->saveAs($file);
                $model->logo = $imageName.'.'.$model->logo_photo->extension;
                $model->save(false);
            }



            $logMessage = "Site config was updated";
            \app\models\Logs::writeLog(
                Yii::$app->user->username, 
                null, 
                $logMessage, 
                time()
            );
            return $this->redirect(['index']);
        }
        return $this->renderIsAjax('site-config', [
            'model' => $model,
        ]);

    }

  
    public function actionSideBar(){
        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            $sideBar = Yii::$app->request->post("isClosed");


            $cookies = Yii::$app->response->cookies;
            // add a new cookie to the response to be sent
            $cookies->add(new \yii\web\Cookie([
                'name' => 'sideBar',
                'value' => $sideBar,
            ]));
        }
    }


    // Notfication actions start

    public function actionEmptyContracts(){
        $query = \app\models\Users::find()
        ->where(['is', 'contract_number', new \yii\db\Expression('null')]);

        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>25]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('empty-contract', ['model' => $model,'pages'=>$pages]);
    }


    public function actionPermitted(){
      $query = \app\models\Users::find()
      ->select('users.*,users_services_packets.status')
      ->leftJoin("users_services_packets","users.id=users_services_packets.user_id")
      ->orderBy(['users.id'=>SORT_DESC])
      ->where(['users.status'=>2])
      ->andWhere(['users_services_packets.status'=>1])
      ->withByLocation();

        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>25]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('permitted', ['model' => $model,'pages'=>$pages]);
    }

    public function actionFailProcesses(){

      $query = \app\models\FailProcess::find()
      ->select('fail_process.*,users.fullname as user_fullname,members.fullname as member_fullaname')
      ->leftJoin('users','users.id=fail_process.user_id')
      ->leftJoin('members','members.id=fail_process.member_id')
      ->where(['fail_process.status'=>'0']);

        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>25]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('fail-process', ['model' => $model,'pages'=>$pages]);
    }

    public function actionAllCredits(){

      $query =  \app\models\StoreItemCount::find()
      ->select('store_item_count.*,users.fullname as usern_name,users.status as user_status,store_item.name as item_name')
      ->leftJoin('users','users.id=store_item_count.user_id')
      ->leftJoin('store_item','store_item.id=store_item_count.item_id')
      ->where(['store_item_count.credit'=>'1','store_item_count.status'=>6,'users.status'=>3])
      ->withByLocation();

        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>25]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('all-credits', ['model' => $model,'pages'=>$pages]);
    }


    public function actionAllGifts(){

      $query =  \app\models\StoreItemCount::find()
      ->select('store_item_count.*,users.fullname as usern_name,users.status as user_status,store_item.name as item_name')
      ->leftJoin('users','users.id=store_item_count.user_id')
      ->leftJoin('store_item','store_item.id=store_item_count.item_id')
      ->where(['store_item_count.credit'=>'2','store_item_count.status'=>4,'users.status'=>3])
      ->withByLocation();

        $pages = new Pagination(['totalCount' => $query->count(),'pageSize'=>25]);
        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('all-gifts', ['model' => $model,'pages'=>$pages]);
    }

    // Notfication actions end

    public function actionChangeTheme(){
        if ( Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_model = \webvimark\modules\UserManagement\models\User::find()->where(['id' => Yii::$app->user->id])->one();
            $user_model->default_theme = Yii::$app->request->post("themeData");
            if ( $user_model->save(false)) {
                Yii::$app->view->theme = new \yii\base\Theme([
                'pathMap' => ['@app/views' => '@app/themes/' . Yii::$app->request->post("themeData")],
                ]);
                die;
            }
        }
    }

    public function actionUserProfile(){
        $model = \webvimark\modules\UserManagement\models\User::find()->where(['id' => Yii::$app->user->id])->one();
        $model->scenario = \webvimark\modules\UserManagement\models\User::SCENARIO_USER_PROFILE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->photo_file = \yii\web\UploadedFile::getInstance($model, 'photo_file');
            if ($model->photo_file) {
                $imageName = Yii::$app->security->generateRandomString(6);
                $file = Yii::getAlias('@app') . '/web/uploads/users/profile/' . $imageName . '.' . $model->photo_file->extension;
                $thumbFile = Yii::getAlias('@app') . '/web/uploads/users/profile/thumbnail/' . $imageName . '.' . $model->photo_file->extension;
                $model->photo_file->saveAs($file);
                $model->photo_url = $imageName . '.' . $model->photo_file->extension;
                $model->save();
                Image::thumbnail($file, 400, 400)->save($thumbFile, ['quality' => 100]);
            } else {
                $model->photo_url = $model->photo_url;
            }
            $model->save(false);

            return $this->redirect(['index']);

        }
        return $this->render('user_profile', ['model' => $model]);
    }

    public function actionChangePassword(){
        $model = \webvimark\modules\UserManagement\models\User::find()->where(['id' => Yii::$app->user->id])->one();
        $model->scenario = \webvimark\modules\UserManagement\models\User::SCENARIO_USER_CHANGE_PASSWORD;
        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->setPassword(Yii::$app->request->post('User')['new_password']);
            $model->save(false);
            return $this->redirect(['index']);
            // return ['code'=>'success','title'=>Yii::t("app","Successfully !"),'message'=>Yii::t("app","Password has changed")];
        }

        return $this->render('change-password', compact('model'));
    }

    public function actionValidatePasswordForm(){
        $model = \webvimark\modules\UserManagement\models\User::find()->where(['id' => Yii::$app->user->id])->one();
        $model->scenario = \webvimark\modules\UserManagement\models\User::SCENARIO_USER_CHANGE_PASSWORD;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionReadCronjobLogs(){
        $file = Yii::getAlias('@runtime') . '/cron_exec_info.txt';
        return $this->renderIsAjax('read-cron-job-logs', ['file' => $file]);
    }



    public function actionIndex(){

        $memberActivtyCount = \app\models\PersonalUserActivty::find()
        ->select('sum( case  when personal_activty.type = "0" then 1 else 0 end ) as activtyConnection,sum( case  when personal_activty.type = "1" then 1 else 0 end ) as activtyDamageRequest,sum( case  when personal_activty.type = "2" then 1 else 0 end ) as activtySellingDeviceInstallation,sum( case  when personal_activty.type = "3" then 1 else 0 end ) as activtyReconnect,sum( case  when personal_activty.type = "4" then 1 else 0 end ) as activtyNewService')
        ->leftJoin('personal_activty','personal_activty.id=personal_user_activty.activty_id ')
        ->where(['member_id'=>Yii::$app->user->id])
        ->asArray()
        ->one();



        return $this->render('index', ['memberActivty'=>$memberActivtyCount]);
    }


    public function actionMemberAtConnection( $year = 2022 ){
        if ($year == null) {
            $year = date("Y");
        }
        $personalUserActivty = \app\models\PersonalUserActivty::find()
        ->select('MONTHNAME(FROM_UNIXTIME(created_at)) as month,personal_activty.type as type')
        ->leftJoin('personal_activty','personal_activty.id=personal_user_activty.activty_id')
        ->where(['member_id'=>Yii::$app->user->id])
        ->andWhere(['type'=>'0'])
        ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(personal_activty.created_at), "%Y")' => $year ])
        ->asArray()
        ->all();
    



        $result = [];
        foreach( $personalUserActivty as $val) {
          if(array_key_exists("month", $val)){
              $result[$val["month"]][] = $val;
          }else{
              $result[""][] = $val;
          }
        }

        $allMonth = [];
        $allData = [];
        foreach ($result as $key => $u) {
            $allMonth[] = substr($key, 0,3);
            $allData[] = count($u);
          
        }

        $yAxsis = [
            [
                "name"=>Yii::t("app","At connection"),
                "data"=>$allData
            ],
        ];


        $data = [
            'yAxsis'=> $yAxsis,
            'stockedAxisCategories'=> $allMonth,
        ];

        return json_encode($data);

    }



    public function actionMemberAtDamage( $year = 2022 ){
        if ($year == null) {
            $year = date("Y");
        }


        $personalUserActivty = \app\models\PersonalUserActivty::find()
        ->select('MONTHNAME(FROM_UNIXTIME(created_at)) as month,personal_activty.type as type')
        ->leftJoin('personal_activty','personal_activty.id=personal_user_activty.activty_id')
        ->where(['member_id'=>Yii::$app->user->id])
        ->andWhere(['type'=>'1'])
        ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(personal_activty.created_at), "%Y")' => $year ])
        ->asArray()
        ->all();
    
        $result = [];
        foreach( $personalUserActivty as $val) {
          if(array_key_exists("month", $val)){
              $result[$val["month"]][] = $val;
          }else{
              $result[""][] = $val;
          }
        }

        $allMonth = [];
        $allData = [];
        foreach ($result as $key => $u) {
            $allMonth[] = substr($key, 0,3);
            $allData[] = count($u);
          
        }
        
        $yAxsis = [
            [
                "name"=>Yii::t("app","At connection"),
                "data"=>$allData
            ],
        ];


        $data = [
            'yAxsis'=> $yAxsis,
            'stockedAxisCategories'=> $allMonth,
        ];

        return json_encode($data);

    }


    public function actionNewUsersChart( $year = null ){
        if ($year == null) {
            $year = date("Y");
        }
        $usersModel = \app\models\Users::find()
        ->select('MONTHNAME(FROM_UNIXTIME(created_at)) as month')
        ->where(['DATE_FORMAT(FROM_UNIXTIME(users.created_at), "%Y")' => $year ])
        ->asArray()
        ->all();


        $result = [];
        foreach( $usersModel as $val) {
          if(array_key_exists("month", $val)){
              $result[$val["month"]][] = $val;
          }else{
              $result[""][] = $val;
          }
        }

        $allMonth = [];
        $allData = [];
        foreach ($result as $key => $u) {
            $allMonth[] = substr($key, 0,3);
            $allData[] = count($u);
          
        }
        $yAxsis = [
            [
                "name"=>Yii::t("app","New user count"),
                "data"=>$allData
            ],
        ];


        $data = [
            'yAxsis'=> $yAxsis,
            'stockedAxisCategories'=> $allMonth,
        ];

        return json_encode($data);
    }


    public function actionUsersStatusChart( $year = null ){
        if ($year == null) {
            $year = date("Y");
        }
        $usersStatusHistoryGraphich = \app\models\UsersStatusHistory::find()
        ->where(['DATE_FORMAT(FROM_UNIXTIME(users_status_history.created_at), "%Y")' => $year ])
        ->asArray()
        ->all();


        $active = [];
        $deactive = [];
        $archive = [];
        $pending = [];
        $vip = [];
        $black_list = [];
        $damage = [];
        $new_service = [];
        $stockedAxisCategories = [];

        foreach ($usersStatusHistoryGraphich as $graphicKey => $userStatusGroup ) {
            $stockedAxisCategories[] = date("M",$userStatusGroup['created_at']);
            $active[] = $userStatusGroup['active_count'];
            $deactive[] = $userStatusGroup['deactive_count'];
            $archive[] = $userStatusGroup['archive_count'];
            $pending[] = $userStatusGroup['pending_count'];
            $vip[] = $userStatusGroup['vip_count'];
            $damage[] = $userStatusGroup['damage_count'];
            $black_list[] = $userStatusGroup['black_list_count'];
            $new_service[] = $userStatusGroup['new_service'];
           
        }

        $yAxsis = [
            [
                "name"=>Yii::t("app","Active"),
                "data"=>$active
            ],
            [
                "name"=>Yii::t("app","Deactive"),
                "data"=>$deactive
            ],
            [
                "name"=>Yii::t("app","Archive"),
                "data"=>$archive
            ],

            [
                "name"=>Yii::t("app","Pending"),
                "data"=>$pending
            ],
            [
                "name"=>Yii::t("app","Vip"),
                "data"=>$vip
            ],

            [
                "name"=>Yii::t("app","Black"),
                "data"=>$black_list
            ],

            [
                "name"=>Yii::t("app","Damage"),
                "data"=>$damage
            ],

            [
                "name"=>Yii::t("app","New service"),
                "data"=>$new_service
            ],

        ];


        $data = [
            'yAxsis'=> $yAxsis,
            'stockedAxisCategories'=> $stockedAxisCategories,
        ];

        return json_encode($data);
    }

    public function actionMonthlyBonusBalance($year = null){
        if ($year == null) {
            $year = date("Y");
        }

        $u_b = \app\models\UserBalance::find()->orderBy(['created_at' => SORT_ASC])
            ->leftJoin('users', 'users.id=user_balance.user_id')
            ->andWhere(['!=', 'user_balance.bonus_in', '0'])
            ->andWhere(['!=', 'user_balance.status', '1'])
            ->withByLocation()
            ->andWhere(['DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), "%Y")' => $year])
            ->asArray()
            ->all();
    
        $month = [];
        foreach ($u_b as $key => $balance) {
            for ($i = 1; $i <= 12; $i++) {
                if ($i == date('n', $balance["created_at"])) {
                    if (isset($month[$i])) {
                        $month[$i]["a"] += round( $balance["bonus_in"], 2 );
                    } else {
                        $month[$i] = ['y' => date('M', $balance["created_at"]), 'a' => round( $balance["bonus_in"], 2 )];
                    }
                }
            }
        }
        $result = [];
        foreach ($month as $key => $value) {
            $result[] = $value;
        }
        return json_encode($result);
    }



    public function actionMonthlyDeductedBonusBalance($year = null){
        if ($year == null) {
            $year = date("Y");
        }

        $u_b = \app\models\UserBalance::find()->orderBy(['created_at' => SORT_ASC])
            ->leftJoin('users', 'users.id=user_balance.user_id')
            ->andWhere(['!=', 'user_balance.bonus_out', '0'])
            ->andWhere(['!=', 'user_balance.status', '1'])
            ->withByLocation()
            ->andWhere(['DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), "%Y")' => $year])
            ->asArray()
            ->all();
    
        $month = [];
        foreach ($u_b as $key => $balance) {
            for ($i = 1; $i <= 12; $i++) {
                if ($i == date('n', $balance["created_at"])) {
                    if (isset($month[$i])) {
                        $month[$i]["a"] += round($balance["bonus_out"], 2);
                    } else {
                        $month[$i] = ['y' => date('M', $balance["created_at"]), 'a' => round( $balance["bonus_out"], 2 )];
                    }
                }
            }
        }
        $result = [];
        foreach ($month as $key => $value) {
            $result[] = $value;
        }
        return json_encode($result);
    }



    public function actionMonthlyBalance( $year = null ){
        if ($year == null) {
            $year = date("Y");
        }

        $u_b = \app\models\UserBalance::find()->orderBy(['created_at' => SORT_ASC])
            ->leftJoin('users', 'users.id=user_balance.user_id')
            ->andWhere(['!=', 'user_balance.status', '1'])
            ->withByLocation()
            ->andWhere(['DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), "%Y")' => $year])
            ->asArray()
            ->all();

        $month = [];
        foreach ($u_b as $key => $balance) {
            for ($i = 1; $i <= 12; $i++) {
                if ($i == date('n', $balance["created_at"])) {
                    if (isset($month[$i])) {
                        $month[$i]["a"] += round( $balance["balance_in"], 2 );
                    } else {
                        $month[$i] = ['y' => date('M', $balance["created_at"]), 'a' => round( $balance["balance_in"], 2 ),];
                    }
                }
            }
        }
        $result = [];
        foreach ($month as $key => $value) {
            $result[] = $value;
        }
        return json_encode($result);
    }

    public function actionServiceMonthlyBalance($year, $service_name){
        $result = [];
        $month = [];
        if ($service_name == "Internet") {
            $pay_for = 0;
        } elseif ($service_name == "Wifi") {
            $pay_for = 2;

        } elseif ($service_name == "TV") {
            $pay_for = 1;
        } elseif ($service_name == "Items") {
            $pay_for = 3;
        }

        $u_b = \app\models\UserBalance::find()
        ->leftJoin('users', 'users.id=user_balance.user_id')
        ->withByLocation()
        ->andWhere(['DATE_FORMAT(FROM_UNIXTIME({{%user_balance}}.created_at), "%Y")' => $year])
        ->andWhere(['pay_for' => $pay_for])
        ->andWhere(['!=','balance_out', 0])
        ->orderBy(['created_at' => SORT_ASC])
        ->asArray()
        ->all();
        
        foreach ($u_b as $key => $balance) {
            for ($i = 1; $i <= 12; $i++) {
                if ($i == date('n', $balance["created_at"])) {
                    if (isset($month[$i])) {
                        $month[$i]["a"] += round($balance["balance_out"], 2);
                    } else {
                        $month[$i] = ['y' => date('M', $balance["created_at"]), 'a' => round($balance["balance_out"], 2)];
                    }
                }
            }
        }
        foreach ($month as $key => $value) {
            $result[] = $value;
        }
        return json_encode($result);

    }

    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }


    public function beforeLogout()
    {
        // Kullanıcının çıkış yapmadan önce veritabanında 'logged_out' alanını güncelle
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $user->logged_out = 1;
            $user->save(false); // 'false' kullanarak doğrulamayı devre dışı bırakın, böylece diğer alanlar değişmez
        }
        return parent::beforeLogout();
    }
    

}
