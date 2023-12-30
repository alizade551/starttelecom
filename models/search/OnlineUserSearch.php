<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersInet;
use kartik\daterange\DateRangeBehavior;
/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class OnlineUserSearch extends UsersInet{
    /**
     * {@inheritdoc}
     */
    public $fullname;
    public $contract_number;
    public $paid_time_type;
    public $updated_at;
    public $credit_status;
    public $balance;
    public $tariff;


    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;


    public function rules()
    {
        return [
            [['id', 'user_id', 'router_id', 'packet_id', 'u_s_p_i', 'status'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['login', 'password', 'mac_address', 'static_ip', 'created_at','credit_status','fullname','paid_time_type','updated_at','contract_number','balance','tariff'], 'safe'],
        ];



        // return [
        //     [['id', 'balance', 'tariff', 'city_id', 'district_id', 'status','location_id','bank_status','bonus','paid_day','paid_time_type'], 'integer'],
    
        //     [['fullname','second_status', 'company', 'phone', 'email', 'room', 'updated_at','services_n','district','location','city','inet_login','contract_number','credit_status'], 'safe'],
        // ];

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

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params){
        $routerModel = \app\models\Routers::find()->where(['id'=>Yii::$app->request->get("id")])->one();
        $activeUsersModel = \app\components\MikrotikQueries::pppActivesPrint(  $routerModel["nas"], $routerModel["username"], $routerModel["password"] );



        $activeUserNames = [];
        $activeUsers = [];
        foreach ( $activeUsersModel as $activeUserKey => $activeUser ) {
            $activeUsers[$activeUser['name']]["name"] = $activeUser['name'];
            $activeUsers[$activeUser['name']]["caller-id"] = $activeUser['caller-id'];
            $activeUsers[$activeUser['name']]["uptime"] = $activeUser['uptime'];
            $activeUsers[$activeUser['name']]["address"] = $activeUser['address'];
            $activeUserNames[] = $activeUser['name'];
        }

   
        $query = UsersInet::find()
        ->select('users_inet.*,users.fullname as fullname,users.credit_status as credit_status,users.tariff as tariff,users.balance as balance,users.status as user_status,users.paid_time_type as paid_time_type,users.updated_at as updated_at,users.contract_number as contract_number')
        ->leftJoin('users','users.id=users_inet.user_id')
        ->andWhere(['users_inet.login'=>$activeUserNames])
        ->andWhere(['users_inet.router_id'=>$routerModel['id']])
        ->orderBy(['users_inet.login'=>'SORT_ASC']);                   

   


        $dataProvider = new \app\components\CustomActiveDataProvider([
            'query' => $query, 
            'activeUsers'=>$activeUsers,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_online_users', 20),
            ],
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);




        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'router_id' => $this->router_id,
            'packet_id' => $this->packet_id,
            'u_s_p_i' => $this->u_s_p_i,
            'users.status' => $this->status,
            'users.credit_status' => $this->credit_status,
            'users.paid_time_type' => $this->paid_time_type,
            'users.tariff' => $this->tariff,
            'users.balance' => $this->balance,
        ]);

        if ($this->createTimeRange) {

         $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $this->createTimeEnd]]);
        }      

        // $query->andFilterWhere(['like', 'users.fullname', $this->fullname])
        //     ->andFilterWhere(['like', 'users.company', $this->company])
        //     ->andFilterWhere(['like','users_inet.login', $this->inet_login])
        //     ->andFilterWhere(['like', 'users.phone', $this->phone])
        //     ->andFilterWhere(['like', 'users.email', $this->email])
        //     ->andFilterWhere(['like', 'users.room', $this->room])
        //     ->andFilterWhere(['users_sevices.service_id' => $this->services_n])
        //     ->andFilterWhere(['like','users.contract_number', $this->contract_number])
        //     ->andFilterWhere(['like', 'address_locations.name', $this->location])
        //     ->andFilterWhere(['like', 'address_district.district_name', $this->district])
        //     ->andFilterWhere(['like', 'address_cities.city_name', $this->city])
        //     ->andFilterWhere(['like', 'users.created_at', $this->created_at]);



        $query->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like','users.contract_number', $this->contract_number])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like','users.contract_number', $this->contract_number])
            ->andFilterWhere(['like', 'mac_address', $this->mac_address])
            ->andFilterWhere(['like', 'static_ip', $this->static_ip])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->asArray();

        return $dataProvider;
    }
}
