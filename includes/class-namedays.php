<?php
namespace COUPONEMAILS;

class Namedays
{

	protected function remove_accents($str)
	{
		$unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A',
		'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ř'=>'R', 'Ď'=>'D', 'Ľ'=>'L', 'Ĺ'=>'L', 'Ť'=>'T', 'Ň'=>'N',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Č'=>'C',
		'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ď'=>'d', 'ç'=>'c', 'ĺ'=>'l',
		'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ŕ'=>'r',
		'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ľ'=>'l', 'č'=>'c', 'ť'=>'t', 'ě'=>'e', 'ň'=>'n', 'ř'=>'r', 'ů'=>'u');
		$str = strtr( $str, $unwanted_array );
		return $str;
	}

	public function get_names_for_day( $d, $m, $with_alt = true)
	{
		$options = get_option('namedayemail_options');
		$calendar =new \COUPONEMAILS\Calendars();
		
		if (! isset($options['language']) )
			return;
		$language = $options['language'];
		switch ($language) {
			case 1:
			$names = $calendar->get_slovak_namedays_array();
				break;
			case 2:
			$names = $calendar->get_czech_namedays_array();
				break;
			case 3:
			$names = $calendar->get_hungarian_namedays_array();
				break;
			case 4:
			$names = $calendar->get_austrian_namedays_array();
				break;
		}
		
		$names_str = $names->{"$d" . "." . "$m" . "."};

		if ($with_alt == true) {
			//$names_str = $names_str . ", " . $this->remove_accents($names_str);
			$names_str = $names_str . ", " . \remove_accents($names_str);
			

			$arr    = explode(',', $names_str);
			$trimmed_array = array_map('trim', $arr);
			$names_str = implode(',', array_unique($trimmed_array));
		}
		return ($names_str);
	}

	public function get_celebrating_users($d, $m, $with_alt = true)
	{
		global $wpdb;
	
		$names_str = $this->get_names_for_day($d, $m, $with_alt);
		if (empty($names_str))
			return;
		$names_array = explode(",", $names_str);
		$names = sprintf("'%s'", implode("','", $names_array ) );
		$sql = "SELECT u.id, umfn.meta_value  AS user_firstname, u.user_email AS user_email FROM {$wpdb->prefix}users AS u
				INNER JOIN {$wpdb->prefix}usermeta AS umfn ON
					umfn.user_id = u.id AND umfn.meta_key = 'first_name'
					WHERE umfn.meta_value IN ($names)
					AND umfn.meta_value <> '' 
					AND umfn.meta_value IS NOT NULL";
		EmailFunctions::test_add_log('-get_celebrating_users- ' . $this->type . PHP_EOL  . $sql);
		$result = $wpdb->get_results($sql, OBJECT);

		return $result;
	}
	
	function namedayemail_event_setup()
	{
		$options = get_option('namedayemail_options');
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$str_nameday =  date('Y-m-d',strtotime('+' . $options['days_before'] . ' day'));
			
			$dateValue = strtotime($str_nameday);
			$m = intval(date("m", $dateValue));
			$d = intval(date("d", $dateValue));
			$funcs = new EmailFunctions('namedayemail');
			
			$users = $this->get_celebrating_users($d,$m);
			foreach ($users as $user) {
				if (!empty($user->user_firstname))
					$funcs->couponemails_create($user);
			}
			$funcs->couponemails_delete_expired();
		}
	}	
}
?>