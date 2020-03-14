<?php 

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']));
define('PUN_ALLOW_INDEX', 1);
define('PUN_ACTIVE_PAGE', 'index');
require PUN_ROOT.'header.php';

$x = $pun_user['charx'];
$y = $pun_user['chary'];
$result = $db->query('SELECT name, legend, text, properties FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.$y) or error('Unable to fetch locations list', __FILE__, __LINE__, $db->error());
//$num_locs = $db->num_rows($result);

//for ($i = 0; $i < $num_locs; ++$i)
//	$loc_list[] = $db->fetch_assoc($result);

$cur_loc = $db->fetch_assoc($result);

////////

$west = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.($x-1).' AND y='.$y) or error('Unable to fetch locations list', __FILE__, __LINE__, $db->error());
$west_loc = $db->fetch_assoc($west);

$east = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.($x+1).' AND y='.$y) or error('Unable to fetch locations list', __FILE__, __LINE__, $db->error());
$east_loc = $db->fetch_assoc($east);

$north = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.($y-1)) or error('Unable to fetch locations list', __FILE__, __LINE__, $db->error());
$north_loc = $db->fetch_assoc($north);

$south = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.($y+1)) or error('Unable to fetch locations list', __FILE__, __LINE__, $db->error());
$south_loc = $db->fetch_assoc($south);


$dir = isset($_GET['dir']) ? $_GET['dir'] : null;

if (($dir == 'west')&&($west_loc['name'] != '')) {
	$db->query('UPDATE '.$db->prefix.'users SET charx='.($x-1).' WHERE id='.$pun_user['id']) or error('Unable to change user group', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'east')&&($east_loc['name'] != '')) {
	$db->query('UPDATE '.$db->prefix.'users SET charx='.($x+1).' WHERE id='.$pun_user['id']) or error('Unable to change user group', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'north')&&($north_loc['name'] != '')) {
	$db->query('UPDATE '.$db->prefix.'users SET chary='.($y-1).' WHERE id='.$pun_user['id']) or error('Unable to change user group', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'south')&&($south_loc['name'] != '')) {
	$db->query('UPDATE '.$db->prefix.'users SET chary='.($y+1).' WHERE id='.$pun_user['id']) or error('Unable to change user group', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

$healhp = $pun_user['charmaxhp'] - $pun_user['charhp'];

function hasheal() {
	global $healhp, $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], '+') && ($healhp > 0) && ($pun_user['chargold'] >= ($healhp*2)));
}

function hasfight() {
	global $cur_loc;
	return hasproperties($cur_loc['properties'], 'F');
}

if (($dir == 'heal') && (hasheal())) {
	if ($healhp > 0) {
		$db->query('UPDATE '.$db->prefix.'users SET charhp='.($pun_user['charmaxhp']).',chargold='.($pun_user['chargold'] - ($healhp*2)).' WHERE id='.$pun_user['id']) or error('Unable to change user group', __FILE__, __LINE__, $db->error());
	}
	header('Location: game_index.php');
	exit();
}



ob_start();

//echo '<h1>Location:</h1>';
//foreach ($loc_list as $cur_loc) {
//	echo pun_htmlspecialchars($cur_loc['name']).'<br/>';
//}

?>



<div id="column-1">
	<div class="blockmenu">
		<h2><span>Локації</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
				<?php if($west_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=west">(Зх) <?php echo $west_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($east_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=east">(Сх) <?php echo $east_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($north_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=north">(Пн) <?php echo $north_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($south_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=south">(Пд) <?php echo $south_loc['name']; ?></a></li>
				<?php }; ?>
				</ul>
			</div>
		</div>
		
		<?php if (hasheal()) {?>
		<h2><span>Вилікувати</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=heal"><?php echo ($healhp); ?> здор. за <?php echo ($healhp * 2);?> зол.</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasfight()) {?>
		<h2><span>Атакувати</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=fight"><?php echo enemyname(). ' ('.$pun_user['charlevel'].')'; ?></a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
	</div>
</div>
<div id="column-2">
	<div class="blockform">
		<h2><span><?php echo pun_htmlspecialchars($cur_loc['name']); ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend><?php echo pun_htmlspecialchars($cur_loc['legend']); ?></legend>
					<div class="infldset">
						<p><?php echo pun_htmlspecialchars($cur_loc['text'].$cur_loc['properties']); ?></p>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<div id="column-3">
	<div class="blockmenu">
		<?php echo characterbox($pun_user); ?>	
	</div>
</div>









<?php
$tpl_main = str_replace('<pun_main>', trim(ob_get_contents()), $tpl_main);
ob_end_clean();
require PUN_ROOT.'footer.php';
?>