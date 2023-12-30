<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\search\UsersMessageSearch;
use app\models\UsersMessage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsersSmsController implements the CRUD actions for UsersSms model.
 */
class UsersMessageController extends DefaultController
{

    /**
     * Lists all UsersSms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UsersSms model.
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

    public function actionCreateValidate()
    {
        $model = new \app\models\UsersMessage();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }



    /**
     * Creates a new UsersSms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        $model = new UsersMessage();
        $templates = \app\models\MessageTemplate::find()
        ->where(['!=', 'name', 'packet_info'])
        ->andWhere(['!=', 'name', 'contract_info'])
        ->andWhere(['!=', 'name', 'balance_alert'])
        ->asArray()
        ->all();

        $siteConfig = \app\models\SiteConfig::find()->one();
        if (  $model->load( Yii::$app->request->post() ) ) {

            $status = Yii::$app->request->post('UsersMessage')['user_status'];
            $locations = Yii::$app->request->post('UsersMessage')['locations'];

            $userModel = \app\models\Users::find()
                ->where(['status' => $status ])
                ->andWhere(['location_id' => $locations])
                ->all();

            foreach ($userModel as $key => $user ) {
              $filtredNumber = str_replace( "+", "", $user->phone );
                if ( $model->type == "sms" ) {
                    $messageTemplateModel = \app\models\MessageTemplate::find()
                    ->where(['name'=>Yii::$app->request->post('UsersMessage')['template']])
                    ->andWhere(['lang'=>$user['message_lang']])
                    ->asArray()
                    ->one();

                    $parameters = [];
                    if ( isset( Yii::$app->request->post('UsersMessage')['dynamic_param'] ) ) {
                        foreach ( explode(",", $messageTemplateModel['params']) as $tkey => $pValue) {
                            foreach ( Yii::$app->request->post('UsersMessage')['dynamic_param'] as $key => $value) {
                                if ($tkey == $key) {
                                $parameters[$pValue] = $value;
                                }
                            }
                        }
                    }



                    if ( Yii::$app->request->post('UsersMessage')['text'] != "" ) {
                        $templateSmsAsText = \app\components\Utils::createText( Yii::$app->request->post('UsersMessage')['text'] );
                    }else{
                        if ( count($parameters) > 0 ) {
                           $templateSmsAsText = \app\components\Utils::createText( $messageTemplateModel['sms_text'],$parameters );

                        }else{
                           $templateSmsAsText = \app\components\Utils::createText( $messageTemplateModel['sms_text'] );
                        }
                    }


                    \app\models\UsersMessage::sendSms(
                        $user->id, 
                        Yii::$app->user->username, 
                        $filtredNumber, 
                        $templateSmsAsText,
                        json_encode($parameters) 
                    );                 
                }

                if ( $model->type == "whatsapp" ) {
                    $messageTemplateModel = \app\models\MessageTemplate::find()
                    ->where(['name'=>Yii::$app->request->post('UsersMessage')['template']])
                    ->andWhere(['lang'=>$user['message_lang']])
                    ->asArray()
                    ->one();

                    $parameters = [];
                    $whatsappTemplateParam = [];
                    if ( isset( Yii::$app->request->post('UsersMessage')['dynamic_param'] ) ) {
                        foreach ( Yii::$app->request->post('UsersMessage')['dynamic_param'] as $key => $dParam ) {
                            $parameters[$key]['type'] = "text";
                            $parameters[$key]['text'] = $dParam; 
                            $whatsappTemplateParam['{{'.($key+1).'}}'] = $dParam;
                        }
                    }

                    $template = [
                      'name'=>$messageTemplateModel['name'],
                      'language'=>['code'=>$messageTemplateModel['lang']],
                      "components"=>[
                        ['type'=>'header'],
                        [
                            'type'=>'body',
                            'parameters'=>$parameters
                        ],
                      ]
                    ];
   
                    $templateWhatsappAsText = \app\components\Utils::createText( 
                        $messageTemplateModel['whatsapp_body_text'],
                        $whatsappTemplateParam
                    );

      

                   \app\components\Utils::sendWhatsappMessage( 
                        $template, 
                        $user->id, 
                        Yii::$app->user->username, 
                        $filtredNumber, 
                        $templateWhatsappAsText,
                        json_encode($whatsappTemplateParam)
                    );
                }


            }

             return $this->redirect(['index']);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'templates' => $templates,
        ]);
    }

    public function actionSendAgain($id){
        $model = \app\models\UsersMessage::find()->where(['id'=>$id])->one();
        if ( \Yii::$app->request->isAjax && \Yii::$app->request->isPost  ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $filtredNumber = str_replace( "+", "", $model->user->phone );
            $params = json_decode( $model->params, true );

            if ( $model->type == "sms" ) {
               return  \app\models\UsersMessage::sendSms(
                    $model->user_id, 
                    $model->member_name, 
                    $filtredNumber, 
                    $model->text ,
                    $params,
                    $model->id

                ); 
            }

            if ( $model->type == "whatsapp" ) {
                $findTemplate = \app\models\MessageTemplate::find()
                ->andWhere("MATCH(whatsapp_body_text) AGAINST ('$model->text' IN BOOLEAN MODE)")
                ->asArray()
                ->one();
                
                $parameters = [];
                $textParams = [];
                $i =1;
                foreach ( $params as $key => $param ) {
                   $parameters[]  = [ 'type'=>'text','text'=>$param ];
                   $textParams[$key]  = $param;
                }
                $template = [
                  'name'=>$findTemplate['name'],
                  'language'=>['code'=>$findTemplate['lang']],
                  "components"=>[
                    ['type'=>'header'],
                    [
                        'type'=>'body',
                        'parameters'=>$parameters
                    ],
                  ]
                ];

                $templateWhatsappAsText = \app\components\Utils::createText( 
                    $findTemplate['whatsapp_body_text'],
                    $textParams,
       
                );
            
                $checkWhatsappMessage = \app\components\Utils::sendWhatsappMessage( 
                    $template, 
                    $model->id, 
                    Yii::$app->user->username, 
                    $filtredNumber, 
                    $templateWhatsappAsText,
                    $textParams,
                    $model->id,
                );

                return $checkWhatsappMessage;
            }

        }
       return $this->renderIsAjax('send-again', [
            'model' => $model
        ]);
    }



    public function actionSuccessful()
    {
        return $this->render('successful', [
        ]);
    }

    /**
     * Updates an existing UsersSms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = UsersMessage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
