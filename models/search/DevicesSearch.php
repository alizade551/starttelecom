<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Devices;
use kartik\daterange\DateRangeBehavior;

/**
 * DevicesSearch represents the model behind the search form of `app\models\Devices`.
 */
class DevicesSearch extends Devices
{

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $district;
    public $location;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'port_count', 'pon_port_count', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['name','vendor_name', 'type', 'description','ip_address','published','district','location'], 'safe'],
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
    public function search($params)
    {
        $query = Devices::find();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_devices';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
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
            'port_count' => $this->port_count,
            'pon_port_count' => $this->pon_port_count,
            'created_at' => $this->created_at,
        ]);


        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(devices.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(devices.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  


        $query->andFilterWhere(['like', 'devices.name', $this->name])
            ->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
            ->andFilterWhere(['like', 'address_district.district_name', $this->district])
            ->andFilterWhere(['like', 'address_locations.name', $this->location])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'published', $this->published])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
