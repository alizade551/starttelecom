<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserBalance;
use kartik\daterange\DateRangeBehavior;
/**
 * UserBalanceSearch represents the model behind the search form of `app\models\UserBalance`.
 */
class UserBalanceSearch extends UserBalance
{

    public $user_name;
    public $member_fullname;
    public $item_name;
    public $receipt_code;
    public $transaction;
    public $member;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['id', 'user_id', 'pay_for', 'item_usage_id', 'created_at','receipt_id','status'], 'integer'],
            [['balance_in', 'balance_out','bonus_in','bonus_out'], 'number'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['payment_method','user_name','item_name','receipt_code','transaction','member','member_fullname'], 'safe'],
        ];
    }


    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'createTimeRange',
                'dateFormat'=>'Y-m-d',
                'dateStartFormat'=>'Y-m-d',
                'dateEndFormat'=>'Y-m-d',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params){
        $query = UserBalance::find()->select('user_balance.*,users.fullname as user_name,items.name as item_name,receipt.code as receipt_code,members.fullname as member_fullaname')
        ->leftJoin('receipt','receipt.id=user_balance.receipt_id')
        ->leftJoin('users','users.id=user_balance.user_id')
        ->leftJoin('members','members.id=receipt.member_id')
        ->leftJoin('item_usage','item_usage.id=user_balance.item_usage_id')
        ->leftJoin('items','items.id=item_usage.item_id')
        ->withByLocation()
        ->asArray();          
        

        $cookieName = '_grid_page_size_user-balance';

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);



        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
         $dataProvider->sort->attributes['user_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['users.fullname' => SORT_ASC],
            'desc' => ['users.fullname' => SORT_DESC],
        ]; 
        $dataProvider->sort->attributes['item_name'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['items.name' => SORT_ASC],
            'desc' => ['items.name' => SORT_DESC],
        ]; 

        $dataProvider->sort->attributes['receipt_code'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['receipt.code' => SORT_ASC],
            'desc' => ['receipt.code' => SORT_DESC],
        ]; 

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_balance.id' => $this->id,
            'user_id' => $this->user_id,
            'user_balance.balance_in' => $this->balance_in,
            'user_balance.balance_out' => $this->balance_out,
            'user_balance.bonus_in' => $this->bonus_in,
            'user_balance.bonus_out' => $this->bonus_out,
            'user_balance.pay_for' => $this->pay_for,
            'user_balance.status' => $this->status,
            'user_balance.created_at' => $this->created_at,
        ]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(user_balance.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(user_balance.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  

        $query->andFilterWhere(['like', 'user_balance.payment_method', $this->payment_method])
        ->andFilterWhere(['like', 'items.name', $this->item_name])
        ->andFilterWhere(['like', 'receipt.code', $this->receipt_code])
        ->andFilterWhere(['like', 'transaction', $this->transaction])
        ->andFilterWhere(['like', 'members.fullname', $this->member])
        ->andFilterWhere(['like', 'users.fullname', $this->user_name]);

        return $dataProvider;
    }
}
