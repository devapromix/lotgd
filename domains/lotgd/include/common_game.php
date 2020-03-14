<?php

function enemyname(){
	return 'Ворог';
}

function charactermaxexp($l){
	return $l * 250;
}

function characterracename($u) {
	$raceid = $u['charrace'];
	if ($raceid == 0)
		return 'Ельф';
	else
		return '?';
}

function characterclassname($u) {
	$classid = $u['charclass'];
	if ($classid == 0)
		return 'Воїн';
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
	if (!$u['is_guest']) {
		$p .= '<h2><span>'.$u['charname'].'</span></h2>';
		$p .= '<div class="box">';
		$p .= '<div class="inbox">';
		$p .= '<ul>';
		$p .= '<li><p>'.characterracename($u).' '.characterclassname($u).' '.$u['charlevel'].' рівня</p></li>';
		$p .= '<li><p>Досвід '.$u['charexp'].'/'.charactermaxexp($u['charlevel']).'</p></li>';
		$p .= "<li><p>Здоров'я ".$u['charhp'].'/'.$u['charmaxhp']."</p></li>";
		$p .= "<li><p>Золото ".$u['chargold']."</p></li>";
		$p .= '</ul>';
		$p .= '</div>';
		$p .= '</div>';
	} else {
		
	}
	return $p;
}

?>