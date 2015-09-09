<?php

namespace Addons\Core\Models;

use Addons\Core\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\Authenticatable;
//use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Addons\Core\Models\Role;

class User extends Model implements AuthenticatableContract/*, CanResetPasswordContract*/
{
	use Authenticatable/*, CanResetPassword*/;
	use SoftDeletes, EntrustUserTrait;

	/**
	 * Cache enabled
	 *
	 * @var boolean
	 */
	public $auto_cache = true;


	//能批量赋值
	//protected $fillable = ['username', 'password'];
	//不能批量赋值
	protected $guarded = ['id'];
	protected $dates = ['lastlogin_at'];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'deleted_at'];

	public function get($username)
	{
		return $this->where('username', $username)->first();
	}

	public function add($data, $role_name = Role::VIEWER)
	{
		$data['password'] = bcrypt($data['password']);
		$user = $this->create($data);
		//加入view组
		$user->attachRole(Role::where('name', $role_name)->firstOrFail());
		return $user;
	}

	public function auto_password($username)
	{
		$username = strtolower($username);
		return md5($username.env('APP_KEY').md5($username));
	}

	public function gender()
	{
		return $this->hasOne(dirname(get_class($this)).'\\Field', 'id', 'gender');
	}
}

//自动创建extra等数据
User::created(function($user){
	/*UserExtra::create([
		'id' => $user->id,
	]);*/
});