<?php 

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';

$page_title = 'Край Серого Дракона';
define('PUN_ALLOW_INDEX', 1);
define('PUN_ACTIVE_PAGE', 'index');
require PUN_ROOT.'header.php';

$x = $pun_user['charx'];
$y = $pun_user['chary'];
$result = $db->query('SELECT name, legend, text, properties FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.$y) or error('EN:2730168344', __FILE__, __LINE__, $db->error());
//$num_locs = $db->num_rows($result);

//for ($i = 0; $i < $num_locs; ++$i)
//	$loc_list[] = $db->fetch_assoc($result);

$cur_loc = $db->fetch_assoc($result);

////////

$west = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.($x-1).' AND y='.$y) or error('EN:4588902356', __FILE__, __LINE__, $db->error());
$west_loc = $db->fetch_assoc($west);

$east = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.($x+1).' AND y='.$y) or error('EN:3017485479', __FILE__, __LINE__, $db->error());
$east_loc = $db->fetch_assoc($east);

$north = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.($y-1)) or error('EN:2281057941', __FILE__, __LINE__, $db->error());
$north_loc = $db->fetch_assoc($north);

$south = $db->query('SELECT name, legend, text FROM '.$db->prefix.'locations WHERE x='.$x.' AND y='.($y+1)) or error('EN:3337926577', __FILE__, __LINE__, $db->error());
$south_loc = $db->fetch_assoc($south);

function hashp() {
	global $pun_user;
	return $pun_user['charhp'] > 0;
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : null;

if (($dir == 'west') && ($west_loc['name'] != '') && hashp()) {
	$db->query('UPDATE '.$db->prefix.'users SET charx='.($x-1).' WHERE id='.$pun_user['id']) or error('EN:3263826781', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'east') && ($east_loc['name'] != '') && hashp()) {
	$db->query('UPDATE '.$db->prefix.'users SET charx='.($x+1).' WHERE id='.$pun_user['id']) or error('EN:4483267890', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'north') && ($north_loc['name'] != '') && hashp()) {
	$db->query('UPDATE '.$db->prefix.'users SET chary='.($y-1).' WHERE id='.$pun_user['id']) or error('EN:1890437890', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

if (($dir == 'south') && ($south_loc['name'] != '') && hashp()) {
	$db->query('UPDATE '.$db->prefix.'users SET chary='.($y+1).' WHERE id='.$pun_user['id']) or error('EN:3485267811', __FILE__, __LINE__, $db->error());
	header('Location: game_index.php');
	exit();
}

$healhp = $pun_user['charmaxhp'] - $pun_user['charhp'];

function hasheal() {
	global $healhp, $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], '+') && ($healhp > 0) && ($pun_user['chargold'] >= ($healhp*2)));
}

function hasfight() {
	global $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], 'F') && ($pun_user['charhp'] > 0));
}

if (($dir == 'heal') && (hasheal()) && (hashp())) {
	if ($healhp > 0) {
		$db->query('UPDATE '.$db->prefix.'users SET charhp='.($pun_user['charmaxhp']).',chargold='.($pun_user['chargold'] - ($healhp*2)).' WHERE id='.$pun_user['id']) or error('EN:4181567392', __FILE__, __LINE__, $db->error());
	}
	header('Location: game_index.php');
	exit();
}

if (($dir == 'revive') && (!hashp())) {
	$pun_user['charhp'] = $pun_user['charmaxhp'];
	$pun_user['charx'] = 0;
	$pun_user['chary'] = 0;
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',charx='.$pun_user['charx'].',chary='.$pun_user['chary'].' WHERE id='.$pun_user['id']) or error('EN:5178453451', __FILE__, __LINE__, $db->error());	
	redirect('game_index.php', 'Ты был воскрешен!');
}

$hasfight = '';
if (($dir == 'fight') && (hasfight()) && hashp()) {
	$hasfight = '';
	$dam = 26;
	$pun_user['charhp'] = $pun_user['charhp'] - $dam;
	if ($pun_user['charhp'] <= 0) {
		$hasfight .= 'Ты погиб!'.'<br/>';
		$pun_user['charhp'] = 0;
		if ($pun_user['chargold'] > 0) {
			$pun_user['chargold'] = 0;
			$hasfight .= 'Ты потерял все золото!'.'<br/>';
		}
	} else {
		$hasfight .= 'Ты победил!'.'<br/>';
		$gold = rand(10, 20);
		$hasfight .= 'Золото +'.$gold.'<br/>';	
		$pun_user['chargold'] = $pun_user['chargold'] + $gold;
	}
	
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',chargold='.$pun_user['chargold'].' WHERE id='.$pun_user['id']) or error('EN:2390144337', __FILE__, __LINE__, $db->error());	
}


ob_start();

//echo '<h1>Location:</h1>';
//foreach ($loc_list as $cur_loc) {
//	echo pun_htmlspecialchars($cur_loc['name']).'<br/>';
//}

?>



<div id="column-1">
	<div class="blockmenu">
	
		<?php if ($pun_user['charhp'] > 0) {?>
		<h2><span>Идти</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
				<?php if($west_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=west">← <?php echo $west_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($east_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=east">→ <?php echo $east_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($north_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=north">↑ <?php echo $north_loc['name']; ?></a></li>
				<?php }; ?>
				<?php if($south_loc['name'] != ''){ ?>
					<li><a href="game_index.php?dir=south">↓ <?php echo $south_loc['name']; ?></a></li>
				<?php }; ?>
				</ul>
			</div>
		</div>
		<?php } else {; ?>
		<h2><span>Воскрешение</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=revive">Воскресить</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasheal()) {?>
		<h2><span>Исцелить</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=heal"><?php echo ($healhp); ?> ♥ за <?php echo ($healhp * 2);?> зол.</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasfight()) {?>
		<h2><span>Атаковать</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=fight"><?php echo enemyname(); ?></a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
	</div>
</div>
<div id="column-2">

	<?php if ($pun_user['is_guest']) { ?>
	<div class="blockform">
		<h2><span>Здравствуй, путник!"</span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Добро пожаловать в "Край Серого Дракона!</legend>
					<div class="infldset">
						<p>Земли Эльвиона полны тайн, интриг, алчности и правосудия! Здесь каждый смельчак может стать героем, сыскав славу в сражениях и подвигах! Древние подземелья, сотни врагов, несметные сокровища - все это, и даже больше, можно встретить, исследуя этот таинственный мир.</p>
						<p>Готов ли ты стать героем и увековечить свое имя на гранитной плите Зала Славы? Выбор за тобой...</p>
						<ul>
							<li><h2>→ <a href="register.php">Регистрация</a></h2></li>
							<li><h2>→ <a href="login.php">Вход</a></h2></li>
						</ul>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend>Последние события в Эльвионе</legend>
					<div class="infldset">
						<ul>
						<?php
							$p = '';
							$msgs = $db->query('SELECT * FROM '.$db->prefix.'recent_incidents') or error('EN:3481045687', __FILE__, __LINE__, $db->error());
							while ($msg = mysqli_fetch_array($msgs)) {
								$p .= '<li><p>'.$msg['message'].'</p></li>';
							}
							echo $p;
						?>
						</ul>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<?php } else {?>
	<div class="blockform">
		<h2><span><?php echo pun_htmlspecialchars($cur_loc['name']); ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend><?php echo pun_htmlspecialchars($cur_loc['legend']); ?></legend>
					<div class="infldset">
						<p><?php echo pun_htmlspecialchars($cur_loc['text']); ?></p>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	
	<?php if ($hasfight != '') { ?>
	<div class="blockform">
		<h2><span><?php echo 'Поединок'; ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend><?php echo 'Кричащий Бегун <small>Птица 1 уровня</small>'; ?></legend>
					<div class="infldset">
						<p><?php echo $hasfight; ?></p>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php } ?>
	
</div>
<div id="column-3">
	<div class="blockmenu">
		<?php
		if (!$pun_user['is_guest']) {
			echo characterbox($pun_user);
		} else {
			echo '<h2><span>Зал Славы</span></h2>';
			$topchars = $db->query('SELECT charname,charexp FROM '.$db->prefix.'users  order by charexp desc limit 7') or error('EN:3122763926', __FILE__, __LINE__, $db->error());
			$p = "";
			$n = 0;
			$p .= '<div class="box">';
			$p .= '<div class="inbox">';
			$p .= '<ul>';
			while ($curchar = mysqli_fetch_array($topchars)) {
				$n++;
				$p .= '<li><p><b>'.$n.'. '.$curchar['charname'].'</b></p></li>';
			}
			$p .= '</ul>';
			$p .= '</div>';
			$p .= '</div>';
			echo $p;
			echo '<h2><span>Случайный Герой</span></h2>';
			$result = $db->query('SELECT charname,charrace,charclass,charlevel FROM '.$db->prefix.'users  order by rand() limit 1') or error('EN:4714503458', __FILE__, __LINE__, $db->error());
			$r = mysqli_fetch_array($result);
			$r['is_guest'] = true;
			echo characterbox($r);
		}
		?>
	</div>
</div>









<?php
$tpl_main = str_replace('<pun_main>', trim(ob_get_contents()), $tpl_main);
ob_end_clean();
require PUN_ROOT.'footer.php';
?>