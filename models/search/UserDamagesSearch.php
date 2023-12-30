<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserDamages;
use kartik\daterange\DateRangeBehavior;


/**
 * UserDamagesSearch represents the model behind the search form of `\app\models\UserDamages`.
 */
class UserDamagesSearch extends UserDamages
{
    public $fullname;
    public $member_fullname;



    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'member_id', 'status'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['personal', 'damage_reason', 'damage_result', 'message', 'created_at','fullname','member_fullname'], 'safe'],
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
        $query = UserDamages::find()        
        ->select('user_damages.*,users.fullname as user_fullname')
        ->leftjoin('users','users.id=user_damages.user_id')
        ->leftjoin('members','members.id=user_damages.member_id')
        ->where(['<>','user_damages.status', 0])
        ->groupBy('user_damages.id');;

        // add conditions that should always apply here
        $cookieName = '_grid_page_size_sloved_reports';
        
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
            'user_id' => $this->user_id,
            'member_id' => $this->member_id,
            'user_damages.status' => $this->status,
        ]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(user_damages.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(user_damages.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  

        $query->andFilterWhere(['like', 'personal', $this->personal])
            ->andFilterWhere(['like', 'users.fullname', $this->fullname])
            ->andFilterWhere(['like', 'members.fullname', $this->member_fullname])
            ->andFilterWhere(['like', 'damage_reason', $this->damage_reason])
            ->andFilterWhere(['like', 'damage_result', $this->damage_result])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'user_damages.created_at', $this->created_at]);

        return $dataProvider;
    }
}
