<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Receipt;
use kartik\daterange\DateRangeBehavior;
/**
 * ReceiptSearch represents the model behind the search form of `app\models\Receipt`.
 */
class ReceiptSearch extends Receipt
{

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $member;



    public function rules()
    {
        return [
            [['id', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['code', 'status','member','seria','type'], 'safe'],
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
        $query = Receipt::find()
        ->joinWith('member');

        // add conditions that should always apply here
        $cookieName = '_grid_page_size_receipt';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue( $cookieName, 20 )
            ],
        ]);

        $dataProvider->sort->attributes['member'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['members.username' => SORT_ASC],
            'desc' => ['members.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->createTimeRange) {
             $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(receipt.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(receipt.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
        } 


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'seria', $this->seria])
            ->andFilterWhere(['like', 'members.username', $this->member])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'receipt.status', $this->status]);

        return $dataProvider;
    }
}
