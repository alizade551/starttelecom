<?php

namespace webvimark\modules\UserManagement\components;

use Yii;
use yii\web\User;
use webvimark\modules\UserManagement\models\rbacDB\Role;

/**
 * Class UserConfig
 * @package webvimark\modules\UserManagement\components
 */
class UserConfig extends User{
    /**
     * @inheritdoc
     */
    public $identityClass = 'webvimark\modules\UserManagement\models\User';

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $cookieLifetime = 604800;

    /**
     * @inheritdoc
     */
    public $loginUrl = ['/user-management/auth/login'];

    /**
     * Allows to call Yii::$app->user->isSuperadmin
     *
     * @return bool
     */
    public function getIsSuperadmin()
    {
        return @Yii::$app->user->identity->superadmin == 1;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return @Yii::$app->user->identity->username;
    }

    public function getUserRole()
    { 
        return array_keys( Role::getUserRoles(Yii::$app->user->identity->id)); 

    }




    public function getPhoto()
    {
        return @Yii::$app->user->identity->photo_url;
    }

    public function getFullname()
    {
        return @Yii::$app->user->identity->fullname;
    }
    public function getPhoto_url()
    {
        return @Yii::$app->user->identity->photo_url;
    }

    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        AuthHelper::updatePermissions($identity);

        parent::afterLogin($identity, $cookieBased, $duration);
    }

}
