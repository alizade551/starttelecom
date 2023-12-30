<?php

namespace webvimark\modules\UserManagement\models\search;

use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use webvimark\modules\UserManagement\models\UserVisitLog;
use kartik\daterange\DateRangeBehavior;

/**
 * UserVisitLogSearch represents the model behind the search form about `webvimark\modules\UserManagement\models\UserVisitLog`.
 */
class UserVisitLogSearch extends UserVisitLog
{


    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

	public function rules()
	{
		return [
			[['id'], 'integer'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
			[['token', 'ip', 'language', 'user_id', 'os', 'browser', 'visit_time'], 'safe'],
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

	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search($params)
	{
		$query = UserVisitLog::find();

		$query->joinWith(['user']);

		// Don't let non-superadmin view superadmin activity
		if ( !Yii::$app->user->isSuperadmin )
		{
			$query->andWhere([User::tableName() . '.superadmin'=>0]);
		}


		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size_user_visit', 20),
			],
			'sort'=>[
				'defaultOrder'=>['id'=> SORT_DESC],
			],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}


        if ($this->createTimeRange) {
            $query->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(members_visit_log.visit_time), '%Y-%m-%d')", $this->createTimeStart],['<=', "DATE_FORMAT(FROM_UNIXTIME(members_visit_log.visit_time), '%Y-%m-%d')", $this->createTimeEnd]]);
         }  

		$query->andFilterWhere([
			$this->tableName() . '.id' => $this->id,
		]);

        	$query->andFilterWhere(['like', User::tableName() . '.username', $this->user_id])
			->andFilterWhere(['like', static::tableName() . '.ip', $this->ip])
			->andFilterWhere(['like', static::tableName() . '.os', $this->os])
			->andFilterWhere(['like', static::tableName() . '.browser', $this->browser])
			->andFilterWhere(['like', static::tableName() . '.language', $this->language]);

		return $dataProvider;
	}
}
