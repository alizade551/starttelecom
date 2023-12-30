<?php

namespace webvimark\modules\UserManagement\models;

use webvimark\helpers\LittleBigHelper;
use webvimark\helpers\Singleton;
use webvimark\modules\UserManagement\components\AuthHelper;
use webvimark\modules\UserManagement\components\UserIdentity;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\rbacDB\Route;
use webvimark\modules\UserManagement\UserManagementModule;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $email_confirmed
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property string $bind_to_ip
 * @property string $registration_ip
 * @property integer $status
 * @property integer $superadmin
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends UserIdentity
{
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	const STATUS_BANNED = -1;

	/**
	 * @var string
	 */
	public $gridRoleSearch;

	/**
	 * @var string
	 */
	public $password;
	public $new_password;
	public $current_password;
	public $photo_file;

	public $city_id;
	public $district_id;
	public $location_id;


	/**
	 * @var string
	 */
	public $repeat_password;
	public $new_repeat_password;

	/**
	 * Store result in singleton to prevent multiple db requests with multiple calls
	 *
	 * @param bool $fromSingleton
	 *
	 * @return static
	 */
	public static function getCurrentUser($fromSingleton = true)
	{
		if ( !$fromSingleton )
		{
			return static::findOne(Yii::$app->user->id);
		}

		$user = Singleton::getData('__currentUser');

		if ( !$user )
		{
			$user = static::findOne(Yii::$app->user->id);

			Singleton::setData('__currentUser', $user);
		}

		return $user;
	}

	/**
	 * Assign role to user
	 *
	 * @param int  $userId
	 * @param string $roleName
	 *
	 * @return bool
	 */
	public static function assignRole($userId, $roleName)
	{
		try
		{
			Yii::$app->db->createCommand()
				->insert(Yii::$app->getModule('user-management')->auth_assignment_table, [
					'user_id' => $userId,
					'item_name' => $roleName,
					'created_at' => time(),
				])->execute();

			AuthHelper::invalidatePermissions();

			return true;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Revoke role from user
	 *
	 * @param int    $userId
	 * @param string $roleName
	 *
	 * @return bool
	 */
	public static function revokeRole($userId, $roleName)
	{
		$result = Yii::$app->db->createCommand()
			->delete(Yii::$app->getModule('user-management')->auth_assignment_table, ['user_id' => $userId, 'item_name' => $roleName])
			->execute() > 0;

		if ( $result )
		{
			AuthHelper::invalidatePermissions();
		}

		return $result;
	}

	/**
	 * @param string|array $roles
	 * @param bool         $superAdminAllowed
	 *
	 * @return bool
	 */
	public static function hasRole($roles, $superAdminAllowed = true)
	{
		if ( $superAdminAllowed AND Yii::$app->user->isSuperadmin )
		{
			return true;
		}
		$roles = (array)$roles;

		AuthHelper::ensurePermissionsUpToDate();

		return array_intersect($roles, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROLES,[])) !== [];
	}

	/**
	 * @param string $permission
	 * @param bool   $superAdminAllowed
	 *
	 * @return bool
	 */
	public static function hasPermission($permission, $superAdminAllowed = true)
	{
		if ( $superAdminAllowed AND Yii::$app->user->isSuperadmin )
		{
			return true;
		}

		AuthHelper::ensurePermissionsUpToDate();

		return in_array($permission, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_PERMISSIONS,[]));
	}

	/**
	 * Useful for Menu widget
	 *
	 * <example>
	 * 	...
	 * 		[ 'label'=>'Some label', 'url'=>['/site/index'], 'visible'=>User::canRoute(['/site/index']) ]
	 * 	...
	 * </example>
	 *
	 * @param string|array $route
	 * @param bool         $superAdminAllowed
	 *
	 * @return bool
	 */
	public static function canRoute($route, $superAdminAllowed = true)
	{
		if ( $superAdminAllowed AND Yii::$app->user->isSuperadmin )
		{
			return true;
		}

		$baseRoute = AuthHelper::unifyRoute($route);

		if ( Route::isFreeAccess($baseRoute) )
		{
			return true;
		}

		AuthHelper::ensurePermissionsUpToDate();

		return Route::isRouteAllowed($baseRoute, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROUTES,[]));
	}

	/**
	 * getStatusList
	 * @return array
	 */
	public static function getStatusList()
	{
		return array(
			self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
			self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
			self::STATUS_BANNED   => Yii::t('app', 'Banned'),
		);
	}


	public static function getStatusValue($val)
	{
		$ar = self::getStatusList();

		return isset( $ar[$val] ) ? $ar[$val] : $val;
	}

	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return Yii::$app->getModule('user-management')->user_table;
	}

	/**
	* @inheritdoc
	*/
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}



	const SCENARIO_CREATE = 'new_user';
	const SCENARIO_REGISTER = 'user_register';
	const SCENARIO_USER_PROFILE = 'user_profile';
	const SCENARIO_USER_CHANGE_PASSWORD = 'user_change_password';
	const SCENARIO_CHANGE_PASSWORD = 'change_password';
	const SCENARIO_PERMISSION_LOCATION = 'user_permission_location';


     public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['username','fullname','photo_file','email','password','repeat_password','city_id','district_id','location_id','address','phone','birthday','personal'];
        $scenarios[self::SCENARIO_USER_PROFILE] = ['username','email','photo_file','photo_url','fullname','default_lang','default_theme'];
        $scenarios[self::SCENARIO_USER_CHANGE_PASSWORD] = ['new_password','new_repeat_password','current_password'];
        $scenarios[self::SCENARIO_CHANGE_PASSWORD] = ['password','repeat_password'];

        return $scenarios;
    }



	/**
	* @inheritdoc
	*/
	public function rules(){
		return [
			[['username','email','fullname','birthday','address','phone','status','personal','city_id','district_id'], 'required'],
			[['status', 'email_confirmed'], 'integer'],
			[['city_id', 'district_id','location_id'], 'safe'],
			[['api_access'], 'string'],
			['email', 'email'],
			['email', 'validateEmailConfirmedUnique'],
			['bind_to_ip', 'validateBindToIp'],
			['bind_to_ip', 'trim'],
			[['bind_to_ip','fullname','birthday','address','phone'], 'string', 'max' => 255],
			['password', 'required', 'on'=>[self::SCENARIO_CREATE, self::SCENARIO_CHANGE_PASSWORD]],
			[['city_id', 'district_id','address','phone','birthday','personal'], 'required', 'on' => self::SCENARIO_CREATE],

			['password', 'string', 'max' => 255, 'on'=>[self::SCENARIO_CREATE, self::SCENARIO_CHANGE_PASSWORD]],
			['password', 'trim', 'on'=>[self::SCENARIO_CREATE, self::SCENARIO_CHANGE_PASSWORD]],
			['password', 'match', 'pattern' => Yii::$app->getModule('user-management')->passwordRegexp],

			['new_repeat_password', 'compare', 'compareAttribute'=>'new_password'],

			[['new_password', 'new_repeat_password','current_password',], 'required', 'on' => self::SCENARIO_USER_CHANGE_PASSWORD],
			[['username','email','fullname'], 'required', 'on' => self::SCENARIO_USER_PROFILE],

			 ['current_password', 'validateCurrent', 'on' => self::SCENARIO_USER_CHANGE_PASSWORD ,'skipOnEmpty' => false],

			['repeat_password', 'required', 'on'=>[self::SCENARIO_CREATE, self::SCENARIO_CHANGE_PASSWORD]],
			['repeat_password', 'compare', 'compareAttribute'=>'password'],
		];
	}

	public function validateCurrent()
	{
			$model = User::find()->where(['id'=>Yii::$app->user->id])->one();

            $password = $model->password_hash;  //returns current password as stored in the dbase

       	
			$validateOldPass = Yii::$app->security->validatePassword($this->current_password,$password);
           if(!$validateOldPass){
                $this->addError('current_password', Yii::t("app","Current passwor is wrong") );
           }
	}



	/**
	 * Check that there is no such confirmed E-mail in the system
	 */
	public function validateEmailConfirmedUnique()
	{
		if ( $this->email )
		{
			$exists = User::findOne([
				'email'           => $this->email,
				// 'email_confirmed' => 1,
			]);

			if ( $exists AND $exists->id != $this->id )
			{
				$this->addError('email', Yii::t('app', 'This email has already taken.'));
			}
		}
	}

	/**
	 * Validate bind_to_ip attr to be in correct format
	 */
	public function validateBindToIp()
	{
		if ( $this->bind_to_ip )
		{
			$ips = explode(',', $this->bind_to_ip);

			foreach ($ips as $ip)
			{
				if ( !filter_var(trim($ip), FILTER_VALIDATE_IP) )
				{
					$this->addError('bind_to_ip', UserManagementModule::t('back', "Wrong format. Enter valid IPs separated by comma"));
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'id'                 => 'ID',
			'username'           => Yii::t('app', 'Username'),
			'fullname'           => Yii::t('app', 'User'),
			'personal'           => Yii::t('app', 'Personal'),
			'birthday'           => Yii::t('app', 'Birthday'),
			'phone'              => Yii::t('app', 'Phone number'),
			'address'            => Yii::t('app', 'Address'),
			'city_id'            => Yii::t('app', 'City'),
			'district_id'        => Yii::t('app', 'District'),
			'location_id'        => Yii::t('app', 'Location'),
			'superadmin'         => Yii::t('app', 'Superadmin'),
			'confirmation_token' => Yii::t('app', 'Confirmation Token'),
			'registration_ip'    => Yii::t('app', 'Registration IP'),
			'bind_to_ip'         => Yii::t('app', 'Bind to IP'),
			'status'             => Yii::t('app', 'Status'),
			'gridRoleSearch'     => Yii::t('app', 'Roles'),
			'created_at'         => Yii::t('app', 'Created'),
			'updated_at'         => Yii::t('app', 'Updated'),
			'password'           => Yii::t('app', 'Password'),
			'photo_file'         => Yii::t('app', 'Photo'),
			'current_password'    => Yii::t('app', 'Current password'),
			'repeat_password'    => Yii::t('app', 'Repeat password'),
			'new_password'       => Yii::t('app', 'New password'),
			'new_repeat_password'    => Yii::t('app', 'New password repeat'),
			'email_confirmed'    => Yii::t('app', 'E-mail confirmed'),
			'email'              => Yii::t('app', 'E-mail'),
			'default_theme'              => Yii::t('app', 'Default theme'),

		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRoles()
	{
		return $this->hasMany(Role::className(), ['name' => 'item_name'])
			->viaTable(Yii::$app->getModule('user-management')->auth_assignment_table, ['user_id'=>'id']);
	}

    public function getLocation()
    {
        return $this->hasOne(\app\models\MemberLocation::className(), ['member_id' => 'id']);
    }



    public static function personalStatus(){
        return [
            0=>Yii::t('app','No'),
            1=>Yii::t('app','Yes'),
        ];
    }

    public static function apiStatus(){
        return [
            ''=>Yii::t('app','Select'),
            0=>Yii::t('app','Deactive'),
            1=>Yii::t('app','Active'),
        ];
    }

	/**
	 * Make sure user will not deactivate himself and superadmin could not demote himself
	 * Also don't let non-superadmin edit superadmin
	 *
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if ( $insert )
		{
			if ( php_sapi_name() != 'cli' )
			{
				$this->registration_ip = LittleBigHelper::getRealIp();
			}
			$this->generateAuthKey();
		}
		else
		{
			// Console doesn't have Yii::$app->user, so we skip it for console
			if ( php_sapi_name() != 'cli' )
			{
				if ( Yii::$app->user->id == $this->id )
				{
					// Make sure user will not deactivate himself
					$this->status = static::STATUS_ACTIVE;

					// Superadmin could not demote himself
					if ( Yii::$app->user->isSuperadmin AND $this->superadmin != 1 )
					{
						$this->superadmin = 1;
					}
				}

				// Don't let non-superadmin edit superadmin
				if ( isset($this->oldAttributes['superadmin']) && !Yii::$app->user->isSuperadmin && $this->oldAttributes['superadmin'] == 1 )
				{
					return false;
				}
			}
		}

		// If password has been set, than create password hash
		if ( $this->password )
		{
			$this->setPassword($this->password);
		}

		return parent::beforeSave($insert);
	}

	/**
	 * Don't let delete yourself and don't let non-superadmin delete superadmin
	 *
	 * @inheritdoc
	 */
	public function beforeDelete()
	{
		// Console doesn't have Yii::$app->user, so we skip it for console
		if ( php_sapi_name() != 'cli' )
		{
			// Don't let delete yourself
			if ( Yii::$app->user->id == $this->id )
			{
				return false;
			}

			// Don't let non-superadmin delete superadmin
			if ( !Yii::$app->user->isSuperadmin AND $this->superadmin == 1 )
			{
				return false;
			}
		}

		return parent::beforeDelete();
	}
}
