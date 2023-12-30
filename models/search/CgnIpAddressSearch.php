<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CgnIpAddress;

/**
 * CgnIpAddressSearch represents the model behind the search form of `\app\models\CgnIpAddress`.
 */
class CgnIpAddressSearch extends CgnIpAddress
{
    public $ip_address;
    public $router;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ip_address_id'], 'integer'],
            [['internal_ip', 'port_range', 'inet_login','ip_address','router'], 'safe'],
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
        $query = CgnIpAddress::find()->select('cgn_ip_address.*,ip_adresses.public_ip as public_ip,routers.name as router_name')
        ->leftJoin('ip_adresses','ip_adresses.id=cgn_ip_address.ip_address_id')
        ->leftJoin('routers','routers.id=cgn_ip_address.router_id')
        ->asArray();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_cgn';
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ]
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
            'ip_address_id' => $this->ip_address_id,
        ]);

        $query->andFilterWhere(['like', 'internal_ip', $this->internal_ip])
            ->andFilterWhere(['like', 'port_range', $this->port_range])
            ->andFilterWhere(['like', 'ip_adresses.public_ip', $this->ip_address])
            ->andFilterWhere(['like', 'routers.name', $this->router])
            ->andFilterWhere(['like', 'inet_login', $this->inet_login]);

        return $dataProvider;
    }
}
