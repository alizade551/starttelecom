<?php
namespace app\models\api;
use app\models\UsersSevices as MainUsersSevices;

class UsersSevices extends MainUsersSevices
{

    public function fields()
    {
        return ['user_id','service_id','contract_number','user'];
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }


}
