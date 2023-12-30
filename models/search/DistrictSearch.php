<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\District;

/**
 * LocationSearch represents the model behind the search form of `app\models\Location`.
 */
class DistrictSearch extends District
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'city_id','nas_id'], 'integer'],
            [['district_name'], 'safe'],
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
        $query = District::find()
        ->leftjoin('users','users.district_id=address_district.id')
        ->groupBy('address_district.id')
        ->withByLocation();


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_district', 20),
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'address_district.id' => $this->id,
            'address_district.city_id' => $this->city_id,
            'address_district.nas_id' => $this->nas_id,
        ]);

        $query->andFilterWhere(['like', 'district_name', $this->district_name]);

        return $dataProvider;
    }
}
