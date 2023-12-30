<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\IpAdresses;
use kartik\daterange\DateRangeBehavior;


/**
 * IpAdressesSearch represents the model behind the search form of `app\models\IpAdresses`.
 */
class IpAdressesSearch extends IpAdresses
{
    public $login;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'split', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['public_ip', 'type','status','login'], 'safe'],
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
    
        $query = IpAdresses::find()
        ->select('ip_adresses.*,users_inet.login as login,users_inet.user_id as user_id')
        ->leftJoin('users_inet','users_inet.static_ip=ip_adresses.id')
        ->asArray();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_ip_address';
        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                 'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
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
            'split' => $this->split,
            'created_at' => $this->created_at,
        ]);


        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(ip_adresses.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(ip_adresses.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  


        $query->andFilterWhere(['like', 'public_ip', $this->public_ip])
            ->andFilterWhere(['like', 'ip_adresses.status', $this->status])
            ->andFilterWhere(['like', 'users_inet.login', $this->login])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
