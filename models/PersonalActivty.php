<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "personal_activty".
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int $created_at
 *
 * @property Users $user
 * @property PersonalUserActivty[] $personalUserActivties
 */
class PersonalActivty extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $members;

    public static function tableName()
    {
        return 'personal_activty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'created_at','members'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['type'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'Customer'),
            'members' => Yii::t('app', 'Users'),
            'type' => Yii::t('app', 'Activty type'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }

    public static function Type(){
        return [
            0=>Yii::t('app','At connection'),
            1=>Yii::t('app','At Report'),
            2=>Yii::t('app','At device installation'),
            3=>Yii::t('app','At Re-connecting'),
            4=>Yii::t('app','New service'),
            5=>Yii::t('app','Other'),
        ];
    }


    public static function createActivty( $user_id, $damage_id, $personal )
    {
        $model = \app\models\PersonalActivty::find()
        ->where(['damage_id'=>$damage_id])
        ->andWhere(['user_id'=>$user_id])
        ->one();

        if ( $model != null  ) {
            \app\models\PersonalUserActivty::deleteAll(['activty_id'=>$model->id]);
            foreach ( $personal as $pk => $personal) {
                $personalUserActivty = new \app\models\PersonalUserActivty;
                $personalUserActivty->activty_id  = $model->id;
                $personalUserActivty->member_id  = $personal;
                $personalUserActivty->save(false);
            }
        }else{
            $model = new \app\models\PersonalActivty;
            $model->user_id = $user_id;
            $model->damage_id = $damage_id;
            $model->type = "1";
            $model->created_at = time();
            if ( $model->save(false) ) {
                // \app\models\PersonalUserActivty::deleteAll(['activty_id '=>$model->id]);
                foreach ( $personal as $pk => $personal) {
                    $personalUserActivty = new \app\models\PersonalUserActivty;
                    $personalUserActivty->activty_id  = $model->id;
                    $personalUserActivty->member_id  = $personal;
                    $personalUserActivty->save(false);
                }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonalUserActivties()
    {
        return $this->hasMany(PersonalUserActivty::className(), ['activty_id' => 'id']);
    }
}
