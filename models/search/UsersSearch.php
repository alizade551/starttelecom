<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;
use kartik\daterange\DateRangeBehavior;
/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users{
    /**
     * {@inheritdoc}
     */
    public $district;
    public $location;
    public $city;
    public $services_n;
    public $inet_login;


    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;


    public function rules()
    {
        return [
            [['id', 'balance', 'tariff', 'city_id', 'district_id', 'status','location_id','bank_status','bonus','paid_day','paid_time_type'], 'integer'],
             [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['fullname','second_status', 'company', 'phone', 'email', 'room', 'updated_at','services_n','district','location','city','inet_login','contract_number','credit_status'], 'safe'],
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

    public function scenarios(){
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params){
        $query = Users::find()
        ->joinWith(['usersSevices','city','locations','district','usersInets'])
        ->where(['!=', 'users.status', 3])
        ->andWhere(['!=', 'users.status', 0])
        ->andWhere(['!=', 'users.status', 4])
        ->withByLocation()
        ->orderBy(['address_cities.city_name'=>'SORT_ASC','address_district.district_name'=>'SORT_ASC','address_locations.name'=>'SORT_ASC'])
        ->groupBy('users.id');                   

        if (     
            isset($params['UsersSearch']['fullname']) ||
            isset($params['UsersSearch']['status'])   || 
            isset($params['UsersSearch']['inet_login'])  || 
            isset($params['UsersSearch']['contract_number'])  || 
            isset($params['UsersSearch']['phone']) || 
            isset($params['UsersSearch']['city'])  || 
            isset($params['UsersSearch']['district'])  ||
            isset($params['UsersSearch']['location'])  ||
            isset($params['UsersSearch']['room'])  || 
            isset($params['UsersSearch']['services_n'])  ||
            isset($params['UsersSearch']['tariff'])  || 
            isset($params['UsersSearch']['bank']) ||
            isset($params['UsersSearch']['paid_day']) ||
            isset($params['UsersSearch']['paid_time_type']) ||
            isset($params['UsersSearch']['credit_status']) ||
            isset($params['UsersSearch']['balance'])               
        ) {
              $query = Users::find()
              ->joinWith(['usersSevices','city','locations','district','usersInets'])
              ->andWhere(['!=', 'users.status', 0])
              ->andWhere(['!=', 'users.status', 4])
              ->withByLocation()
              ->orderBy(['address_cities.city_name'=>'SORT_ASC','address_district.district_name'=>'SORT_ASC','address_locations.name'=>'SORT_ASC'])
              ->groupBy('users.id');           
        }

        $query->with(['city','locations','district','serviceOne']);
        $cookieName = '_grid_page_size_users';

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);


        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
        $dataProvider->sort->attributes['district'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['address_district.district_name' => SORT_ASC],
            'desc' => ['address_district.district_name' => SORT_DESC],
        ];            
    
        $dataProvider->sort->attributes['location'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['address_locations.name' => SORT_ASC],
            'desc' => ['address_locations.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['city'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['address_cities.city_name' => SORT_ASC],
            'desc' => ['address_cities.city_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['inet_login'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['users_inet.login' => SORT_ASC],
            'desc' => ['users_inet.login' => SORT_DESC],
        ];  

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'users.id'=>$this->id,
            'users.balance' => $this->balance,
            'users.bonus' => $this->bonus,
            'users.bank_status' => $this->bank_status,
            'users.tariff' => $this->tariff,
            'users.credit_status' => $this->credit_status,
            'users.paid_day' => $this->paid_day,
            'users.paid_time_type' => $this->paid_time_type,
            'users.status' => $this->status,
       
        ]);
        if ($this->createTimeRange) {

         $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $this->createTimeEnd]]);
        }      

        $query->andFilterWhere(['like', 'users.fullname', $this->fullname])
            ->andFilterWhere(['like', 'users.company', $this->company])
            ->andFilterWhere(['like','users_inet.login', $this->inet_login])
            ->andFilterWhere(['like', 'users.phone', $this->phone])
            ->andFilterWhere(['like', 'users.email', $this->email])
            ->andFilterWhere(['like', 'users.room', $this->room])
            ->andFilterWhere(['users_sevices.service_id' => $this->services_n])
            ->andFilterWhere(['like','users.contract_number', $this->contract_number])
            ->andFilterWhere(['like', 'address_locations.name', $this->location])
            ->andFilterWhere(['like', 'address_district.district_name', $this->district])
            ->andFilterWhere(['like', 'address_cities.city_name', $this->city])
            ->andFilterWhere(['like', 'users.created_at', $this->created_at]);

        return $dataProvider;
    }
}
