<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersVoip;
use kartik\daterange\DateRangeBehavior;

/**
 * UsersVoipSearch represents the model behind the search form of `\app\models\UsersVoip`.
 */
class UsersVoipSearch extends UsersVoip
{

    public $fullname;
    public $packet_name;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'packet_id', 'u_s_p_i', 'status', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['phone_number','fullname','packet_name'], 'safe'],
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
        $query = UsersVoip::find()->select('users_voip.*,users.fullname as user_fullname,service_packets.packet_name as packet_name')
        ->leftjoin('users','users.id=users_voip.user_id')
        ->leftjoin('service_packets','service_packets.id=users_voip.packet_id')
        ->groupBy('users_voip.id')
        ->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),

            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(users_voip.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(users_voip.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'users_voip.user_id' => $this->user_id,
            'users_voip.packet_id' => $this->packet_id,
            'users_voip.u_s_p_i' => $this->u_s_p_i,
            'users_voip.status' => $this->status,
            'users_voip.created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'phone_number', $this->phone_number])
        ->andFilterWhere(['like', 'service_packets.packet_name', $this->packet_name])
        ->andFilterWhere(['like', 'users.fullname', $this->fullname]);

        return $dataProvider;
    }
}
