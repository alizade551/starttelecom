<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PersonalActivty;
use kartik\daterange\DateRangeBehavior;



/**
 * PersonalActivtySearch represents the model behind the search form of `\app\models\PersonalActivty`.
 */
class PersonalActivtySearch extends PersonalActivty{

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $user_fullname;
    public $personal;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['type','user_fullname','personal'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = PersonalActivty::find()
        ->joinWith('personalUserActivties')
        ->select('personal_activty.*,users.fullname as user_fullname')
        ->leftjoin('users','users.id=personal_activty.user_id')
        ->leftjoin('members','members.id=personal_user_activty.member_id')
        ->groupBy('personal_activty.id');

        // add conditions that should always apply here

         $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_personal', 20)
            ],
        ]);

        $this->load($params);

        if ( !$this->validate() ) {
            return $dataProvider;
        }


        if ($this->createTimeRange) {
             $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(personal_activty.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(personal_activty.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
        } 

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
        ->andFilterWhere(['like', 'members.fullname', $this->personal])
        ->andFilterWhere(['like', 'users.fullname', $this->user_fullname]);

        return $dataProvider;
    }
}
