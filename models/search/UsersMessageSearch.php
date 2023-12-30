<?php

namespace app\models\search;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersMessage;
use kartik\daterange\DateRangeBehavior;
/**
 * UsersMessageSearch represents the model behind the search form of `app\models\UsersSms`.
 */
class UsersMessageSearch extends UsersMessage
{
    /**
     * {@inheritdoc}
     */

     public $users;
     public $createTimeRange;
     public $createTimeStart;
     public $createTimeEnd;

    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['member_name', 'user_phone', 'message_time','users','type','status','text'], 'safe'],
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
        $query = UsersMessageSearch::find()
        ->select('users_message.*,users.fullname as user_fullname')
        ->leftJoin('users','users.id=users_message.user_id')
        ->withByLocation()
        ->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
              'pagination' => [
                    'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_sms', 20),

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
            'user_id' => $this->user_id,
        ]);
        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`message_time`), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(`message_time`), '%Y-%m-%d')", $this->createTimeEnd]]);
       }   
        $query->andFilterWhere(['like', 'member_name', $this->member_name])
            ->andFilterWhere(['like', 'users.fullname', $this->users])
            ->andFilterWhere(['like', 'user_phone', $this->user_phone])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'users_message.status', $this->status])
            ->andFilterWhere(['like', 'message_time', $this->message_time]);

        return $dataProvider;
    }
}
