<?php
namespace Addons\Core\Models;

use Addons\Core\Models\Model;

class WechatDepotLocation extends Model{
	public $auto_cache = true;
	protected $guarded = [];

	public function depot()
	{
		return $this->belongsTo('App\\WechatDepot', 'wdid', 'id');
	}
}