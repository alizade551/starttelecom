<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Logs;
use kartik\daterange\DateRangeBehavior;
/**
 * LogsSearch represents the model behind the search form of `app\models\Logs`.
 */
class LogsSearch extends Logs
{
    /**
     * {@inheritdoc}
     */
    public $user;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;


    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
             [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['member', 'text', 'time','user'], 'safe'],
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
        $query = Logs::find()->select('logs.*,users.fullname as user_fullname')
        ->leftjoin('users','users.id=logs.user_id')
        ->withByLocation()
        ->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['time'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_logs', 20),
            ],
        ]);


        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
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
        ]);



         if ($this->createTimeRange) {
             $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d')", $this->createTimeEnd]]);
        }  



        $query->andFilterWhere(['like', 'member', $this->member])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'time', $this->time])
            ->andFilterWhere(['like', 'users.fullname', $this->user]);
        return $dataProvider;
    }
}
