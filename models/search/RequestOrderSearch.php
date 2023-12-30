<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RequestOrder;
use kartik\daterange\DateRangeBehavior;
/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class RequestOrderSearch extends RequestOrder
{
    /**
     * {@inheritdoc}
     */
    public $district;
    public $location;
    public $city;
    public $services_n;
    public $contract_number;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public function rules(){
        return [
            [['id', 'balance', 'tariff', 'city_id', 'district_id', 'status','location_id'], 'integer'],
             [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['fullname', 'company', 'phone', 'email','second_status', 'room', 'updated_at','services_n','district','location','city','contract_number','damage_status'], 'safe'],
        ];
    }

   public function behaviors(){
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
    public function scenarios(){
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params){
        $query = RequestOrder::find()
        ->joinWith(['city','locations','district'])
        ->where(['status'=>0])
        ->orWhere(['second_status'=>'5'])
        ->orWhere(['second_status'=>'4'])
        ->orWhere(['second_status'=>'3'])
        // ->orWhere(['damage_status'=>'1'])
        ->withByLocation()
        ->groupBy('users.id');           
        
        // ->distinct()
        $query->with(['city','locations','district']);
        // add conditions that should always apply here

        $cookieName = '_grid_page_size_request_orders';
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
            'sort'=> ['defaultOrder' => ['request_at'=>SORT_ASC]],
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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'balance' => $this->balance,
            'tariff' => $this->tariff,
            'users.status' => $this->status,
       
        ]);
         if ($this->createTimeRange) {
             $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`request_at`), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(`request_at`), '%Y-%m-%d')", $this->createTimeEnd]]);
        }       
        $query->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'second_status', $this->second_status])
            ->andFilterWhere(['like', 'damage_status', $this->damage_status])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'room', $this->room])
            ->andFilterWhere(['like', 'address_locations.name', $this->location])
            ->andFilterWhere(['like', 'address_district.district_name', $this->district])
            ->andFilterWhere(['like', 'address_cities.city_name', $this->city]);

        return $dataProvider;
    }
}
