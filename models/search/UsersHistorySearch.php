<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use kartik\daterange\DateRangeBehavior;
use app\models\UsersHistory;

/**
 * UsersHistorySearch represents the model behind the search form of `app\models\UsersHistory`.
 */
class UsersHistorySearch extends UsersHistory
{

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
            [['text','user'], 'safe'],
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
        $query = UsersHistory::find()
        ->joinWith('user')
        ->withByLocation();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['time'=>SORT_DESC]],
          'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_user_history', 20),

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


        $query->andFilterWhere(['like', 'text', $this->text])
              ->andFilterWhere(['like', 'users.fullname', $this->user]);


        return $dataProvider;
    }
}
