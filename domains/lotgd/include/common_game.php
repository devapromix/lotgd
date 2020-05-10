<?php

$price_room = 50; // Цена съема комнаты в таверне
$price_food = 75; // Цена порции пищи в таверне
$recent_inc = 10; // Кол-во сообщ. последних событий

function gameversion(){
	return 'v.0.0.1';
}

function enemytype($typeid) {
	$r = '';
	switch($typeid) {
		case 0:
			$r = 'Нежить';
			break;
		case 1:
			$r = 'Гуманоид';
			break;
		case 2:
			$r = 'Животное';
			break;
		case 3:
			$r = 'Демон';
			break;
	}
	return $r;
}

function enemyareal($location_type) {
	$r = '';
	switch($location_type) {
		case -1:
			$r = 'Все';
			break;
		case 0:
			$r = 'Кладбище';
			break;
		case 1:
			$r = 'Лес';
			break;
		case 2:
			$r = 'Равнина';
			break;
		case 3:
			$r = 'Степь';
			break;
		case 4:
			$r = 'Пустыня';
			break;
		case 5:
			$r = 'Нагорье';
			break;
		case 6:
			$r = 'Залив';
			break;
		case 7:
			$r = 'Болото';
			break;
		case 8:
			$r = 'Пещера';
			break;
	}
	return $r;
}

function enemyname($u){
	$p = '';
	$p .= '<b>'.$u['charenemyname'].'</b><br/>';
	$p .= '<small>';
	$p .= enemytype($u['charenemytype']).' '.$u['charenemylevel'].' уровня<br/>';
	$p .= '♥ Здоровье '.$u['charenemyhp'];
	$p .= '</small>';
	return $p;
}

function genenemy($location_type) {
	
}

function charactermaxexp($level){
	return $level * 250;
}

function characterracename($u) {
	$raceid = $u['charrace'];
	if ($raceid == 0)
		return 'Эльф';
	else
		return '?';
}

function characterclassname($u) {
	$classid = $u['charclass'];
	if ($classid == 0)
		return 'Воин';
	else
		return '?';
}

function hasproperties($properties, $f) {
	$result = stripos($properties, $f);
	if ($result === false)
		return false;
	else
		return true;
}

function charmenu($menutitle, $linktitle, $link){
	$p = '';
	$p .= '<h2 class="block2"><span>'.$menutitle.'</span></h2>';
	$p .= '<div class="box">';
	$p .= '<div class="inbox">';
	$p .= '<ul>';
	$p .= '<li><a href="'.$link.'">'.$linktitle.'</a></li>';
	$p .= '</ul>';
	$p .= '</div>';
	$p .= '</div>';	
	return $p;
}

function charinfo($pun_user){
	$p = '';
	$p .= '<span data-toggle="tooltip" title="'.characterracename($pun_user).' '.characterclassname($pun_user).' '.$pun_user['charlevel'].' уровня">'.$pun_user['charname'].' </span>';
	$p .= '<span data-toggle="tooltip" title="Опыт '. $pun_user['charexp'].'/'.charactermaxexp($pun_user['charlevel']).'"><img src="img/game/charexp.png"> '.$pun_user['charexp'].' </span>';
	$p .= '<span data-toggle="tooltip" title="Здоровье"><img src="img/game/charhp.png"> '.$pun_user['charhp'].'/'.$pun_user['charmaxhp'].' </span>';
	$p .= '<span data-toggle="tooltip" title="Золото"><img src="img/game/chargold.png"> '.$pun_user['chargold'].' </span>';
	$p .= '<span data-toggle="tooltip" title="Провизия"><img src="img/game/charfood.png"> '.$pun_user['charfood'].'</span>';
	return $p;
}

function characterbox($u){
	$p = '';
	if (!$u['is_guest'])
		$p .= '<h2><span>Персонаж</span></h2>';
	$p .= '<div class="box">';
	$p .= '<div class="inbox">';
	$p .= '<ul>';
	$p .= '<li><p><b>'.$u['charname'].'</b></p></li>';
	$p .= '<li><p>'.characterracename($u).' '.characterclassname($u).' '.$u['charlevel'].' уровня</p></li>';
	if (!$u['is_guest']) {
		$p .= '<li><p>Опыт '.$u['charexp'].'/'.charactermaxexp($u['charlevel']).'</p></li>';
		$p .= "<li><p>Здоровье ".$u['charhp'].'/'.$u['charmaxhp']."</p></li>";
		$p .= "<li><p>Провизия ".$u['charfood']."</p></li>";
		$p .= "<li><p>Золото ".$u['chargold']."</p></li>";
	}
	$p .= '</ul>';
	$p .= '</div>';
	$p .= '</div>';
	return $p;
}

function percent($allvalue, $percent){
	return floor(($allvalue * $percent) / 100);
}

function events(){
	global $db, $recent_inc;
	$p = '';
	$msgs = $db->query('SELECT * FROM '.$db->prefix.'recent_incidents ORDER BY id DESC LIMIT '.$recent_inc) or error('EN:2479345682', __FILE__, __LINE__, $db->error());
	while ($msg = mysqli_fetch_array($msgs)) {
		$p .= '<li><p>'.$msg['message'].'</p></li>';
	}
	return $p;
}

function add_event_msg($msg) {
	global $db, $recent_inc;
	$msgs = $db->query('SELECT * FROM '.$db->prefix.'recent_incidents') or error('Unable to fetch messages list', __FILE__, __LINE__, $db->error());
	$num_rows = mysqli_num_rows( $msgs);
	if ($num_rows >= $recent_inc) {
		$db->query('DELETE FROM '.$db->prefix.'recent_incidents LIMIT 1') or error('Unable to delete message', __FILE__, __LINE__, $db->error());
	}
	$db->query('INSERT INTO '.$db->prefix.'recent_incidents (message) VALUES (\''.$msg.'\')') or error('Unable to add message', __FILE__, __LINE__, $db->error());
}

function add_new_level_msg($name, $level) {
	switch(rand(1, 1)) {
		case 1:
			if ($gender == 0)
				$r = '<b>'.$name.'</b> поднялся на <b>'.$level.'</b> уровень!';
			else
				$r = '<b>'.$name.'</b> поднялась на <b>'.$level.'</b> уровень!';
			break;
	}
	add_event_msg($r);
}

function add_death_msg($name, $gender, $location) {
	switch(rand(1, 3)) {
		case 1:
			if ($gender == 0)
				$r = '<b>'.$name.'</b> умер в локации <b>'.$location.'</b>.';
			else
				$r = '<b>'.$name.'</b> умерла в локации <b>'.$location.'</b>.';
			break;
		case 2:
			if ($gender == 0)
				$r = '<b>'.$name.'</b> погиб в локации <b>'.$location.'</b>.';
			else
				$r = '<b>'.$name.'</b> погибла в локации <b>'.$location.'</b>.';
			break;
		case 3:
			if ($gender == 0)
				$r = '<b>'.$name.'</b> был убит в локации <b>'.$location.'</b>.';
			else
				$r = '<b>'.$name.'</b> была убита в локации <b>'.$location.'</b>.';
			break;
	}
	add_event_msg($r);
}

function add_reg_msg($name, $gender) {
	switch(rand(1, 2)) {
		case 1:
			if ($gender == 0)
				$r = 'Новый герой <b>'.$name.'</b> пришел в <b>Эльвион</b>.';
			else
				$r = 'Новая героиня <b>'.$name.'</b> пришла в <b>Эльвион</b>.';
			break;
		case 2:
			if ($gender == 0)
				$r = 'Новый герой <b>'.$name.'</b> прибыл в <b>Эльвион</b>.';
			else
				$r = 'Новая героиня <b>'.$name.'</b> прибыла в <b>Эльвион</b>.';
			break;
	}
	add_event_msg($r);
}

?>