<?php

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

function charactermaxexp($l){
	return $l * 250;
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
		$p .= "<li><p>Золото ".$u['chargold']."</p></li>";
	}
	$p .= '</ul>';
	$p .= '</div>';
	$p .= '</div>';
	return $p;
}

?>