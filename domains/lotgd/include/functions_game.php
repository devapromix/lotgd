<?php

//
// Check charname
//
function check_charname($charname, $exclude_id = null)
{
	global $db, $pun_config, $errors, $lang_prof_reg, $lang_register, $lang_common, $pun_bans, $lang_register_game;

	// Include UTF-8 function
	require_once PUN_ROOT.'include/utf8/strcasecmp.php';

	// Convert multiple whitespace characters into one (to prevent people from registering with indistinguishable charnames)
	$charname = preg_replace('%\s+%s', ' ', $charname);

	// Validate charname
	if (pun_strlen($charname) < 2)
		$errors[] = $lang_register_game['Charname too short'];
	else if (pun_strlen($charname) > 25) // This usually doesn't happen since the form element only accepts 25 characters
		$errors[] = $lang_register_game['Charname too long'];
	else if (!strcasecmp($charname, 'Guest') || !utf8_strcasecmp($charname, $lang_common['Guest']))
		$errors[] = $lang_register_game['Charname guest'];
	else if (preg_match('%[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}%', $charname) || preg_match('%((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))%', $charname))
		$errors[] = $lang_register_game['Charname IP'];
	else if ((strpos($charname, '[') !== false || strpos($charname, ']') !== false) && strpos($charname, '\'') !== false && strpos($charname, '"') !== false)
		$errors[] = $lang_register_game['Charname reserved chars'];
	else if (preg_match('%(?:\[/?(?:b|u|s|ins|del|em|i|h|colou?r|quote|code|img|url|email|list|\*|topic|post|forum|user)\]|\[(?:img|url|quote|list)=)%i', $charname))
		$errors[] = $lang_register_game['Charname BBCode'];

	// Check charname for any censored words
	if ($pun_config['o_censoring'] == '1' && censor_words($charname) != $charname)
		$errors[] = $lang_register_game['Charname censor'];

	// Check that the charname (or a too similar charname) is not already registered
	$query = (!is_null($exclude_id)) ? ' AND id!='.$exclude_id : '';

	$result = $db->query('SELECT charname FROM '.$db->prefix.'users WHERE (UPPER(charname)=UPPER(\''.$db->escape($charname).'\') OR UPPER(charname)=UPPER(\''.$db->escape(ucp_preg_replace('%[^\p{L}\p{N}]%u', '', $charname)).'\')) AND id>1'.$query) or error('Unable to fetch char info', __FILE__, __LINE__, $db->error());

	if ($db->num_rows($result))
	{
		$busy = $db->result($result);
		$errors[] = $lang_register_game['Charname dupe 1'].' '.pun_htmlspecialchars($busy).'. '.$lang_register_game['Charname dupe 2'];
	}

}

?>