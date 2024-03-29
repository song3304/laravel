<?php
/**
 * 可以输入NULL参数的mktime
 * 当参数为NULL时，代表该参数为当前时间（原函数中，输入NULL，会转化为0）
 * 
 * @param  int $hour   小时
 * @param  int $minute 分
 * @param  int $second 秒
 * @param  int $month  月
 * @param  int $day    日
 * @param  int $year   年
 * @return int         输出时间戳
 */
function mktime_optional($hour = NULL, $minute = NULL, $second = NULL, $month = NULL, $day = NULL, $year = NULL)
{
	$times = getdate();
	!is_null($hour) && $times['hours'] = $hour;
	!is_null($minute) && $times['minutes'] = $minute;
	!is_null($second) && $times['seconds'] = $second;
	!is_null($month) && $times['mon'] = $month;
	!is_null($day) && $times['mday'] = $day;
	!is_null($year) && $times['year'] = $year;

	return mktime( $times['hours'], $times['minutes'], $times['seconds'], $times['mon'], $times['mday'], $times['year'] );
}

/**
 * 本地时间转化为世界时
 * 
 * @param  int $timestamp 本地时间戳
 * @return int            世界时时间戳
 */
function local_to_utc($timestamp = NULL)
{
	return (is_null($timestamp) ? time() : $timestamp) - intval(date('Z'));
}

/**
 * 世界时转化为本地时间
 * 
 * @param  int $timestamp 世界时时间戳
 * @return int            本地时间时间戳
 */
function utc_to_local($timestamp = NULL)
{
	return (is_null($timestamp) ? time() : $timestamp) + intval(date('Z'));
}

/**
 *	给一个$timestamp从00:00到现在的秒数，返回$time时间戳
 * 
 * @param  int $timetick 至凌晨的秒数
 * @param  int $month    月，默认为今天
 * @param  int $day      日，默认为今天
 * @param  int $year     年，默认为今天
 * @return int           返回该时间戳
 */
function timetick_to_timestamp($timetick, $month = NULL, $day = NULL, $year = NULL)
{
	return mktime_optional(0, 0, ($timetick), $month, $day, $year);
}

/**
 * 给一个$timestamp到该年1月1日的天数，返回$timestamp时间戳
 * 
 * @param  int $daytick 至1月1日的天数
 * @param  int $year    年，默认为今年
 * @return int          返回该时间戳
 */
function daytick_to_timestamp($daytick, $year = NULL)
{
	return mktime_optional(0, 0, 0, 1, $daytick, $year);
}

/**
 * 计算出$timestamp距离该日凌晨的秒数
 * 
 * @param  int $timestamp 输入时间戳，默认为当前时间
 * @return int            秒数
 */
function timestamp_to_timetick($timestamp = NULL)
{
	is_null($timestamp) && $timestamp = time();
	$times = getdate($timestamp);
	return $timestamp - mktime(0, 0, 0, $times['mon'], $times['mday'], $times['year']);
}

/**
 * 计算出$timestamp距离该年1月1日的天数
 * @param  int $timestamp 输入时间戳，默认为当前时间
 * @return int            天数
 */
function timestamp_to_daytick($timestamp = NULL)
{
	is_null($timestamp) && $timestamp = time();
	return intval(date('z', $timestamp));
}

/**
 * 按照星期、天、月、年返回时间数组
 * 
 * @param  integer $start        起始时间
 * @param  integer $limit        间隔
 * @param  string $date_type     可以为：day/week/month/year
 * @param  integer $week_start_by 星期的起始时间，0、1、...、6（周日、周一、...、周六）
 * @return array                 [0 => [start, end], 1 => [start, end], ...] 或者 [0 => [start, end], -1 => [start, end], ...]
 */
function date_range($start, $limit, $date_type = 'day', $week_start_by = 1)
{
	$times = getdate($start);
	$result = array();
	$step_list = range(0, $limit);
	switch($date_type)
	{
		case 'week':
			$week = date('w', $start); 
			$_head = mktime(0, 0, 0, $times['mon'], $times['mday'] + (($week_start_by % 7 - $week) % 7), $times['year']); //计算周头数据
			foreach ($step_list as $step)
			{
				$_start = $_head + 86400 * 7 * $step;
				$result[$step] = array($_start, $_start + 86400 * 7 - 1); // 00:00:00 ~ 23:59:59
			}
			break;
		case 'day':
			$_head = mktime(0, 0, 0, $times['mon'], $times['mday'], $times['year']);
			foreach ($step_list as $step)
			{
				$_start = $_head +  86400 * $step;
				$result[$step] = array($_start, $_start + 86400 - 1); // 00:00:00 ~ 23:59:59
			}
			break;
		case 'month':
			foreach ($step_list as $step)
			{
				$_start = mktime(0, 0, 0, $times['mon'] + $step, 1, $times['year']); //计算月头数据
				$_end = mktime(0, 0, 0, $times['mon'] + $step + 1, 1, $times['year']) - 1;
				$result[$step] = array($_start, $_end); // 00:00:00 ~ 23:59:59
			}
			break;
		case 'year':
			foreach ($step_list as $step)
			{
				$_start = mktime(0, 0, 0, 1, 1, $times['year'] + $step); //计算月头数据
				$_end = mktime(0, 0, 0, 1, 1, $times['year'] + $step + 1) - 1;
				$result[$step] = array($_start, $_end); // 00:00:00 ~ 23:59:59
			}
			break;
	}
	$result[0][ $limit >= 0 ? 0 : 1 ] = $start; //将第一项的开始/结束时间设置为参数的时间
	return $result;
}

/**
 * 根据某时间，查找其在哪个时间段，与上面date_range对应
 * 
 * @param  integer $needle    时间戳
 * @param  array $range_data  date_range返回的时间范围数组
 * @return integer            返回对应的KEY
 */
function date_range_search($needle, $range_data)
{
	foreach ($range_data as $k => $v)
		if ($needle >= $v[0] && $needle <= $v[1]) return $k;
	return FALSE;
}