<?php
namespace app\models\api;
use app\models\Users as MainUsers;

class Users extends MainUsers
{

    public function fields()
    {
        return ['fullname'];
    }



}
