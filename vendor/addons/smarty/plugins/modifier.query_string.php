<?php
/**
 * 加密解密
 * 
 * @param  string $string 输入内容
 * @param  string $mode   encode or decode
 * @return string         输出加密或解密内容
 */
function smarty_modifier_query_string($key, $value = NULL)
{
	empty($value) && $value = is_string($key) ? app('request')->input($key) : NULL;
	!empty($value) && $key = [$key => $value]; 
	return http_build_query($key);
}