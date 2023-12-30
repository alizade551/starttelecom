<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Warehouses;

/**
 * WarehousesSearch represents the model behind the search form of `\app\models\Warehouses`.
 */
class WarehousesSearch extends Warehouses
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'location_id', 'created_at'], 'integer'],
            [['name', 'cordinate'], 'safe'],
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
    public function search($params)
    {
        $query = Warehouses::find();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_warehouse';

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
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
            'id' => $this->id,
            'location_id' => $this->location_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'cordinate', $this->cordinate]);

        return $dataProvider;
    }
}
