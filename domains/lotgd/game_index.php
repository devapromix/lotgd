<?php 

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_game_admin.php';

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
	return (hasproperties($cur_loc['properties'], '+') && ($healhp > 0) && ($pun_user['chargold'] >= $healhp));
}

function hasfight() {
	global $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], 'F') && ($pun_user['charhp'] > 0));
}

function hasbuyfood() {
	global $cur_loc, $pun_user, $price_food;
	return (hasproperties($cur_loc['properties'], 'I') && ($pun_user['charhp'] > 0) && ($pun_user['charfood'] < 10) && ($pun_user['chargold'] >= $price_food));
}

function hasrestininn() {
	global $cur_loc, $pun_user, $price_room;
	return (hasproperties($cur_loc['properties'], 'I') && ($pun_user['charhp'] > 0) && ($pun_user['chargold'] >= $price_room));
}

function hasrest() {
	global $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], 'R') && ($pun_user['charfood'] > 0));
}


if (($dir == 'heal') && (hasheal()) && (hashp())) {
	if ($healhp > 0) {
		$db->query('UPDATE '.$db->prefix.'users SET charhp='.($pun_user['charmaxhp']).',chargold='.($pun_user['chargold'] - $healhp).' WHERE id='.$pun_user['id']) or error('EN:4181567392', __FILE__, __LINE__, $db->error());
	}
	header('Location: game_index.php');
	exit();
}

// Покупка еды в Таверне
if (($dir == 'buyfood') && (hasbuyfood())) {
	$pun_user['chargold'] = $pun_user['chargold'] - $price_food;
	$pun_user['charfood']++;
	$db->query('UPDATE '.$db->prefix.'users SET charfood='.$pun_user['charfood'].',chargold='.$pun_user['chargold'].' WHERE id='.$pun_user['id']) or error('EN:3151912713', __FILE__, __LINE__, $db->error());
}

// Воскрешение
if (($dir == 'revive') && (!hashp())) {
	$rdb = $db->query('SELECT gx,gy FROM '.$db->prefix.'regions WHERE id='.$pun_user['charregion']) or error('EN:7726942597', __FILE__, __LINE__, $db->error());
	$gr = $db->fetch_assoc($rdb);	
	$pun_user['charhp'] = percent($pun_user['charmaxhp'], 80);
	$pun_user['charx'] = $gr['gx'];
	$pun_user['chary'] = $gr['gy'];
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',charx='.$pun_user['charx'].',chary='.$pun_user['chary'].' WHERE id='.$pun_user['id']) or error('EN:5178453451', __FILE__, __LINE__, $db->error());	
	redirect('game_index.php', 'Ты был воскрешен!');
}
// Отдых в таверне
$hasrestininn = '';
if (($dir == 'restininn') && (hasrestininn())) {
	$hasrestininn .= 'Вы чувствуете себя отдохнувшим и полным сил.'.'<br/>';
	$pun_user['charhp'] = $pun_user['charmaxhp'];
	$pun_user['chargold'] = $pun_user['chargold'] - $price_room;
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',chargold='.$pun_user['chargold'].' WHERE id='.$pun_user['id']) or error('EN:2967273685', __FILE__, __LINE__, $db->error());
}

// Отдых
$hasrest = '';
if (($dir == 'rest') && (hasrest())) {
	// Отдых - 90%
	if (rand(0,100) <= 10) {
		$hasrest .= 'Вы чувствуете себя отдохнувшим и полным сил.'.'<br/>';
		$pun_user['charhp'] = $pun_user['charmaxhp'];
	// Нападение на Героя во время отдыха - 10%
	} else
		$dir = 'fight';
	$pun_user['charfood']--;
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',charfood='.$pun_user['charfood'].' WHERE id='.$pun_user['id']) or error('EN:6935597915', __FILE__, __LINE__, $db->error());
}

// Бой
$hasfight = '';
if (($dir == 'fight') && (hasfight()) && hashp()) {
	$dam = 12;
	$pun_user['charhp'] = $pun_user['charhp'] - $dam;
	// Поражение
	if ($pun_user['charhp'] <= 0) {
		$hasfight .= 'Ты погиб!'.'<br/>';
		$pun_user['charhp'] = 0;
		$gold = percent($pun_user['chargold'], 20);
		$pun_user['chargold'] = $pun_user['chargold'] - $gold;
		$hasfight .= 'Ты потерял золото!'.'<br/>';
		$exp = percent($pun_user['charexp'], 10);
		$pun_user['charexp'] = $pun_user['charexp'] - $exp;
		$hasfight .= 'Ты потерял опыт!'.'<br/>';
	// Победа
	} else {
		$hasfight .= 'Ты победил!'.'<br/>';
		$gold = rand(10, 20);
		$hasfight .= 'Золото +'.$gold.'<br/>';	
		$pun_user['chargold'] = $pun_user['chargold'] + $gold;
		$exp = rand(15, 25);
		$hasfight .= 'Опыт +'.$exp.'<br/>';	
		$pun_user['charexp'] = $pun_user['charexp'] + $exp;
	}
	
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',chargold='.$pun_user['chargold'].',charexp='.$pun_user['charexp'].' WHERE id='.$pun_user['id']) or error('EN:2390144337', __FILE__, __LINE__, $db->error());	
}


ob_start();

//echo '<h1>Location:</h1>';
//foreach ($loc_list as $cur_loc) {
//	echo pun_htmlspecialchars($cur_loc['name']).'<br/>';
//}

?>



<div id="column-1">
	<div class="blockmenu">

		<?php if ($pun_user['is_guest']) { ?>
		
		<?php
		//echo '<h2><span>Зал Славы</span></h2>';
		$topchars = $db->query('SELECT charname,charexp FROM '.$db->prefix.'users order by charexp desc limit 7') or error('EN:3122763926', __FILE__, __LINE__, $db->error());
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
		//echo $p;
		//echo '<h2 class="block2"><span>Случайный Герой</span></h2>';
		$result = $db->query('SELECT charname,charrace,charclass,charlevel FROM '.$db->prefix.'users  order by rand() limit 1') or error('EN:4714503458', __FILE__, __LINE__, $db->error());
		$r = mysqli_fetch_array($result);
		$r['is_guest'] = true;
		//echo characterbox($r);
		?>		
		
		<?php } else { ?>
	
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
		
		<?php if (hasbuyfood()) {?>
		<h2><span>Купить провизию</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=buyfood">Купить за <?php echo $price_food; ?> зол.</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasheal()) {?>
		<h2><span>Исцелить</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=heal"><?php echo ($healhp); ?> ♥ за <?php echo $healhp;?> зол.</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasrest()) {?>
		<h2><span>Отдых</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=rest">Разжечь огонь</a></li>
				</ul>
			</div>
		</div>
		<?php }; ?>
		
		<?php if (hasrestininn()) {?>
		<h2><span>Снять комнату</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li><a href="game_index.php?dir=restininn">Снять за <?php echo $price_room; ?> зол.</a></li>
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
		<?php }; 
		if ($pun_user['is_admmod']) {
			generate_game_admin_menu();
		}
		?>
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
	<div style="float: right;">
		<span>
			<b data-toggle="tooltip" title="<?php echo characterracename($pun_user).' '.characterclassname($pun_user).' '.$pun_user['charlevel'].' уровня'; ?>"><?php echo $pun_user['charname']; ?></b>
			<span data-toggle="tooltip" title="Опыт <?php echo $pun_user['charexp'].'/'.charactermaxexp($pun_user['charlevel']); ?>"><img src="img/game/charexp.png"> <?php echo $pun_user['charexp']; ?></span>
			<span data-toggle="tooltip" title="Здоровье"><img src="img/game/charhp.png"> <?php echo $pun_user['charhp']; ?>/<?php echo $pun_user['charmaxhp']; ?></span>
			<span data-toggle="tooltip" title="Золото"><img src="img/game/chargold.png"> <?php echo $pun_user['chargold']; ?></span>
			<span data-toggle="tooltip" title="Провизия"><img src="img/game/charfood.png"> <?php echo $pun_user['charfood']; ?></span>
		</span>
	</div>
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
	
	<?php if ($hasrest != '') { ?>
	<div class="blockform">
		<h2><span><?php echo 'Отдых'; ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Вы разожгли огонь</legend>
					<div class="infldset">
						<p><?php echo $hasrest; ?></p>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<?php } ?>	
	
	<?php if ($hasrestininn != '') { ?>
	<div class="blockform">
		<h2><span><?php echo 'Ночь в таверне'; ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Вы хорошо отдохнули</legend>
					<div class="infldset">
						<p><?php echo $hasrestininn; ?></p>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<?php } ?>	
	
	<?php } ?>
	
</div>

<?php
$tpl_main = str_replace('<pun_main>', trim(ob_get_contents()), $tpl_main);
ob_end_clean();
require PUN_ROOT.'footer.php';
?>