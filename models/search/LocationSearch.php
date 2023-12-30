<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Locations;

/**
 * LocationSearch represents the model behind the search form of `app\models\Locations`.
 */
class LocationSearch extends Locations
{

    public $city;
    public $district;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'district_id'], 'integer'],
            [['name','city','district'], 'safe'],
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
        if ( Yii::$app->user->IsSuperadmin ) {
            $query = Locations::find()
            ->leftjoin('address_cities','address_locations.city_id=address_cities.id')
            ->leftjoin('address_district','address_locations.district_id=address_district.id')
            ->groupBy('address_locations.id');
        }else{


            $memberLocation = \app\models\MemberLocation::find()
            ->where(['member_id'=>Yii::$app->user->id])
            ->asArray()
            ->one();

            $cities = [];
            $districts = [];
            $locations = [];

            if ($memberLocation !== null) {
                $cities = explode(",",$memberLocation['city_id']);
                $districts =  explode(",",$memberLocation['district_id']);
                if ($memberLocation['location_id'] != null) {
                     $locations =  explode(",",$memberLocation['location_id']);
                }
            }

            if ($locations != null) {
                $query = Locations::find()
                ->leftjoin('address_cities','address_locations.city_id=address_cities.id')
                ->leftjoin('address_district','address_locations.district_id=address_district.id')
                ->withByLocationId()
                ->groupBy('address_locations.id');
            }else{
                $query = Locations::find()
                ->leftjoin('address_cities','address_locations.city_id=address_cities.id')
                ->leftjoin('address_district','address_locations.district_id=address_district.id')
                ->withByDistrictId()
                ->groupBy('address_locations.id');                
            }

        }


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_location', 20),
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
        $dataProvider->sort->attributes['district'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['address_district.district_name' => SORT_ASC],
            'desc' => ['address_district.district_name' => SORT_DESC],
        ];
        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
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
            'id' => $this->id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
        ->andFilterWhere(['like', 'address_district.district_name', $this->district])
        ->andFilterWhere(['like', 'address_cities.city_name', $this->city]);

        return $dataProvider;
    }
}
