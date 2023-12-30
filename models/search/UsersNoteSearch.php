<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersNote;
use kartik\daterange\DateRangeBehavior;

/**
 * UsersNoteSearch represents the model behind the search form of `app\models\UsersNote`.
 */
class UsersNoteSearch extends UsersNote{

     public $user;
     public $createTimeRange;
     public $createTimeStart;
     public $createTimeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'time'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['member_name', 'note','user'], 'safe'],
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
        $query = UsersNote::find()
        ->joinWith(['user'])
        ->withByLocation();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['time'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_user_note', 20),

            ],
        ]);

        $dataProvider->sort->attributes['user'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['users.fullname' => SORT_ASC],
            'desc' => ['users.fullname' => SORT_DESC],
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
            'user_id' => $this->user_id,
            'time' => $this->time,
        ]);

        if ($this->createTimeRange) {
         $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d')", $this->createTimeEnd]]);
        }  

        $query->andFilterWhere(['like', 'member_name', $this->member_name])
            ->andFilterWhere(['like', 'users.fullname', $this->user])   
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
