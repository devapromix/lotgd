<?php

$price_room = 50; // Цена съема комнаты в таверне
$price_food = 75; // Цена порции пищи в таверне
$recent_inc = 10; // Кол-во сообщ. последних событий

function gameversion(){
	return 'v.0.0.1';
}

function enemyname(){
	$p = '';
	$p .= 'Кричащий Бегун<br/>';
	$p .= '<small>';
	$p .= 'Птица 1 уровня<br/>';
	$p .= '♥ Здоровье 255';
	$p .= '</small>';
	return $p;
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

?>