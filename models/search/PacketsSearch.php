<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Packets;

/**
 * PacketsSearch represents the model behind the search form of `\app\models\Packets`.
 */
class PacketsSearch extends Packets
{
    /**
     * {@inheritdoc}
     */

    public $service_name;

    public function rules()
    {
        return [
            [['id', 'service_id', 'packet_price', 'position', 'created_at'], 'integer'],
            [['packet_name','service_name'], 'safe'],
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
        $query = Packets::find()->select('service_packets.*,services.service_name as service_name,services.service_alias as service_alias')
        ->leftJoin('services','services.id=service_packets.service_id')->asArray();

        // add conditions that should always apply here
        $cookieName = '_grid_page_size_packets';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
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
            'service_id' => $this->service_id,
            'packet_price' => $this->packet_price,
            'position' => $this->position,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'packet_name', $this->packet_name])->andFilterWhere(['like', 'services.service_name', $this->service_name]);

        return $dataProvider;
    }
}
