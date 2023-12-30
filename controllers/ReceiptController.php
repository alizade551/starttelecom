<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\Receipt;
use app\models\search\ReceiptSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ReceiptController implements the CRUD actions for Receipt model.
 */
class ReceiptController extends DefaultController
{

    public $modelClass = 'app\models\Receipt';

    public function actionIndex()
    {
        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Displays a single Receipt model.
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
     * Creates a new Receipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        $model = new Receipt();
        $data = [];
        $codes = [];
        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $start = sprintf("%06d", Yii::$app->request->post('Receipt')['start_int']);
                $end = sprintf("%06d", Yii::$app->request->post('Receipt')['end_int']);
                $seria = trim(Yii::$app->request->post('Receipt')['seria']);
                $check = $model->receiptHave($seria, $start, $end);
                for ($i = $start; $i <= $end; $i++) {
                    $data[] = [$seria,sprintf("%06d", $i),$seria . sprintf("%06d", $i), 0,0, time()];
                    $codes[] = $seria . sprintf("%06d", $i);
                }

                if (array_intersect($codes, $check)) {
                    \Yii::$app->session->setFlash('error', Yii::t('app','Theese receipts has beed added before.Please use another serial numbers'));
                    echo "<div class='custom-alert-error' >" . \Yii::$app->session->getFlash('error') . "</div>";
                   // return $this->redirect(['create']);

                } else {
                    Yii::$app->db->createCommand()->batchInsert('receipt', ['seria','number','code', 'status','type', 'created_at'], $data)->execute();
                    return $this->redirect(['index']);
                }

            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionMemberRecipet(){
        $serias = \app\models\Receipt::find()
        ->where(['status'=>'0'])
        ->andWhere(['member_id'=>null])
        ->groupBy('seria')
        ->asArray()
        ->all();

        $model = new \app\models\Receipt;
        $model->scenario = $model::SCENARIO_DEFINE_MEMBER;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('Receipt');
            $recipets = \app\models\Receipt::find()
            ->where(['and', "number>=".$post['start_int'], "number<=".$post['end_int']])
            ->andWhere(['seria'=>$post['seria']])
            ->asArray()
            ->all();
            $recipet_data = [];
            foreach ($recipets as $key => $recipet) {
                $recipet_data[$key]['id'] = $recipet['id'];
                $recipet_data[$key]['member_id'] = $post['member_id'];
            }
            if (count($recipet_data) > 0) {
                $data = [];
                foreach ($recipet_data as $param) {
                    $data[] = "('" . $param['id'] . "','" . ($param['member_id']) . "')";
                }
                $str = implode(",", $data);
                $sql = 'insert into receipt (id, member_id) values ';
                $sql .= $str . ' ON DUPLICATE KEY UPDATE member_id = VALUES(member_id)';
                $insertCount = Yii::$app->db->createCommand($sql)->execute();
            }
            return $this->redirect(['index']);
        }

        return $this->render('member-recipet', [
            'model' => $model,
            'serias' => $serias,
        ]);
    }

    public function actionGetSeriaDetail(){
        if (Yii::$app->request->isAjax ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $query = \app\models\Receipt::find()
            ->where(['seria' => Yii::$app->request->post('seria')])
            ->andWhere(['member_id'=>null])
            ->andWhere(['status'=>'0']);

            $data['max'] =  $query->max('number');
            $data['min'] =  $query->min('number');
            return $data;
        }
    }
    
    /**
     * Updates an existing Receipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){
        die();
        // $model = $this->findModel($id);
        // $model->scenario = Receipt::SCENARIO_UPDATE;

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['index']);
        // }

        // return $this->renderIsAjax('update', [
        //     'model' => $model,
        // ]);
    }

    public function actionDeleteReceiptFromMember(){
        $model = \app\models\Receipt::find()
        ->select('receipt.*,MAX(number) as max_number,MIN(number) as min_number,members.fullname,members.superadmin as superadmin')
        ->leftJoin('members','members.id=receipt.member_id')
        ->where(['not', ['seria' => null]])
        ->andWhere(['not', ['member_id' => null]])
        ->groupBy(['member_id','seria'])
        ->asArray()
        ->all();

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost   ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            $seria = $post['seria'];
            $min_number = $post['min_number'];
            $max_number = $post['max_number'];

            $condition = [
                'and',
                ['>=', 'number', $min_number],
                ['<=', 'number', $max_number],
            ];

            if (\app\models\Receipt::updateAll(['member_id' => null], $condition) > 0) {
               return [
                'status'=>'success',
                'message'=>Yii::t('app','{seria}{min} - {seria}{max} recipets was removed from member', ['seria'=>$seria,'min'=>$min_number,'max'=>$max_number] ),
                ];
            }
        }


        return $this->render('delete-receipt-from-member',['model'=>$model]);
    }

    /**
     * Deletes an existing Receipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $model->delete();

        return ['status' => 'success'];
    }



}
