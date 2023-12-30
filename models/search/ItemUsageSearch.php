<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ItemUsage;
use kartik\daterange\DateRangeBehavior;
/**
 * ItemUsageSearch represents the model behind the search form of `\app\models\ItemUsage`.
 */
class ItemUsageSearch extends ItemUsage
{
    public $item;
    public $user;
    public $location;
    public $item_stock;

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item_id', 'item_stock_id', 'user_id', 'credit', 'month', 'location_id', 'status', 'created_at'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['quantity', 'mac_address','item','user','location','item_stock'], 'safe'],
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
        $query = ItemUsage::find()
        ->select('item_usage.*,items.name as item_name,users.fullname as user_fullname,address_locations.name as location_name,item_stock.sku as item_stock_sku')
        ->leftjoin('items','items.id=item_usage.item_id')
        ->leftjoin('item_stock','item_stock.id=item_usage.item_stock_id')
        ->leftjoin('address_locations','address_locations.id=item_usage.location_id')
        ->leftjoin('users','users.id=item_usage.user_id')
        ->asArray();

        // add conditions that should always apply here

        $cookieName = '_grid_page_size_item_usage';

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => \Yii::$app->request->cookies->getValue( $cookieName, 20),
            ],
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
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
            'item_id' => $this->item_id,
            'item_stock_id' => $this->item_stock_id,
            'user_id' => $this->user_id,
            'credit' => $this->credit,
            'month' => $this->month,
            'location_id' => $this->location_id,
            'item_usage.status' => $this->status,
            'created_at' => $this->created_at,
        ]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(item_usage.created_at), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(item_usage.created_at), '%Y-%m-%d')", $this->createTimeEnd]]);
         } 

        $query->andFilterWhere(['like', 'quantity', $this->quantity])
            ->andFilterWhere(['like', 'items.name', $this->item])
            ->andFilterWhere(['like', 'users.fullname', $this->user])
            ->andFilterWhere(['like', 'address_locations.name', $this->location])
            ->andFilterWhere(['like', 'item_stock.sku', $this->item_stock])
            ->andFilterWhere(['like', 'mac_address', $this->mac_address]);

        return $dataProvider;
    }
}
