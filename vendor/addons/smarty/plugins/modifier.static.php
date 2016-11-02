<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty static modifier plugin
 *
 * Type:     modifier<br>
 * Name:     static<br>
 * Purpose:  get the absolute URL of static file
 *
 * @author   Fly <fly@load-page.com>
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_static($string, $rewrite = FALSE)
{
	static $static;
	if (empty($static)) $static = config('app.static');
	
	if ($rewrite)
	{
		$urls = explode(',', $string);
		foreach($urls as &$url)
			if (!file_exists(APPPATH.$static.$url))
				$url = 'common/'.$url;
		$string = implode(',', $urls);
	}

	$url = url($static . $string);
	return str_replace('index.php', '', $url);
}

