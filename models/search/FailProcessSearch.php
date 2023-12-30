<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FailProcess;
use kartik\daterange\DateRangeBehavior;


/**
 * FailProcessSearch represents the model behind the search form of `\app\models\FailProcess`.
 */
class FailProcessSearch extends FailProcess
{
    /**
     * {@inheritdoc}
     */

    public $user_fullname;
    public $member_fullname;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public function rules()
    {
        return [
            [['id', 'member_id', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['action', 'params', 'status','user_fullname','member_fullname'], 'safe'],
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
        $query = FailProcess::find()
        ->select('fail_process.*,members.fullname as member_fullname')
        ->leftjoin('members','members.id=fail_process.member_id')
        ->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_fail_process', 20),
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
            'member_id' => $this->member_id,
            'fail_process.created_at' => $this->created_at,
        ]);

        if ($this->createTimeRange) {
         $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(fail_process.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(fail_process.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
        }  

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'users.fullname', $this->user_fullname])
            ->andFilterWhere(['like', 'members.fullname', $this->member_fullname])
            ->andFilterWhere(['like', 'fail_process.status', $this->status]);

        return $dataProvider;
    }
}
