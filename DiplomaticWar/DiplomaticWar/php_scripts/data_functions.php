<?php
//Takes DateTimeFormatted in (mmddyyHHMMSS) and converts it to the unix epoch
//For opposite function see http://www.php.net//manual/en/function.date.php
function dcf_strtounixepoch ($time_str) {
	$h = substr($time_str, 6, 2);
	$min = substr($time_str, 8, 2);
	$s = substr($time_str, 10, 2);
	$mon = intval(substr($time_str, 0, 2));
	$day = intval(substr($time_str, 2, 2));
	$year = intval("20" . substr($time_str, 4, 2));

	return mktime($h, $min, $s, $mon, $day, $year);
}

/*
Generate string with characters a-z A-Z 0-9 of specified length
*/
function dcf_generate_string ($length) {
	$str = '';
	$character_set = array_merge(range("a", "z"), range("A", "Z"), range("0", "9"));
	$set_size = count($character_set);
	
	for ($i=0; $i<$length; $i++) {
		$r_num = rand(0, $set_size-1);
		$str .= $character_set[$r_num];
	}
	
	return $str;
}
?>