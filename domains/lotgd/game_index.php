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

// Ген. нового врага
function gen_new_rand_enemy($areal) {
	global $db, $pun_user;
	$rmob = $db->query('SELECT * FROM '.$db->prefix.'mobs WHERE areal='.$areal.' AND level<='.$pun_user['charlevel'].' order by rand() limit 1') or error('EN:7713504421', __FILE__, __LINE__, $db->error());
	$mob = $db->fetch_assoc($rmob);	
	
	$pun_user['charenemyname'] = $mob['name'];
	$db->query('UPDATE '.$db->prefix.'users SET charenemy='.$mob['id'].',charenemyname="'.$mob['name'].'",charenemytype='.$mob['type'].' WHERE id='.$pun_user['id']) or error('EN:7925487912', __FILE__, __LINE__, $db->error());
}

function ininn() {
	global $cur_loc;
	return hasproperties($cur_loc['properties'], 'I');
}

function hasheal() {
	global $healhp, $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], '+') && ($healhp > 0) && ($pun_user['chargold'] >= $healhp) && hashp());
}

function hasfight() {
	global $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], 'F') && hashp());
}

function hasbuyfood() {
	global $cur_loc, $pun_user, $price_food;
	return (ininn() && hashp() && ($pun_user['charfood'] < 10) && ($pun_user['chargold'] >= $price_food));
}

function hasrestininn() {
	global $cur_loc, $pun_user, $price_room;
	return (ininn() && hashp() && ($pun_user['chargold'] >= $price_room));
}

function hasrest() {
	global $cur_loc, $pun_user;
	return (hasproperties($cur_loc['properties'], 'R') && ($pun_user['charfood'] > 0) && hashp());
}

if (($dir == 'heal') && (hasheal()) && (hashp())) {
	if ($healhp > 0) {
		$db->query('UPDATE '.$db->prefix.'users SET charhp='.($pun_user['charmaxhp']).',chargold='.($pun_user['chargold'] - $healhp).' WHERE id='.$pun_user['id']) or error('EN:4181567392', __FILE__, __LINE__, $db->error());
	}
	header('Location: game_index.php');
	exit();
}

// Покупка еды в Таверне
if (($dir == 'buyfood') && hasbuyfood()) {
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

// Отдых в Таверне
$hasrestininn = '';
if (($dir == 'restininn') && (hasrestininn())) {
	$hasrestininn .= 'Вы чувствуете себя отдохнувшим и полным сил.'.'<br/>';
	$pun_user['charhp'] = $pun_user['charmaxhp'];
	$pun_user['chargold'] = $pun_user['chargold'] - $price_room;
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',chargold='.$pun_user['chargold'].' WHERE id='.$pun_user['id']) or error('EN:2967273685', __FILE__, __LINE__, $db->error());
}

// Отдых
$hasrest = '';
$hasrestmsglev = 0;
if (($dir == 'rest') && (hasrest())) {
	$gr = false;
	// Отдых на кладбище - успех 20%
	if (hasproperties($cur_loc['properties'], 'G')) {
		$gr = true;
		if (rand(0,100) >= 80) {
			$hasrest .= 'Вы отдохнули среди мертвых и набрались сил.'.'<br/>';
			$pun_user['charhp'] = $pun_user['charmaxhp'];		
		} else {
			// Нападение нежити на Героя во время отдыха на кладбище
			$dir = 'fight';
			$hasrestmsglev = 1;
			gen_new_rand_enemy(0);
		}
	}
	// Отдых - успех 90%
	if (!$gr) {
		if (rand(0,100) >= 80) {
			$hasrest .= 'Вы чувствуете себя отдохнувшим и полным сил.'.'<br/>';
			$pun_user['charhp'] = $pun_user['charmaxhp'];
		} else {
			// Нападение вора на Героя во время отдыха
			$dir = 'fight';
			$hasrestmsglev = 2;
			gen_new_rand_enemy(-1);
		}
	}
	$pun_user['charfood']--;
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',charfood='.$pun_user['charfood'].' WHERE id='.$pun_user['id']) or error('EN:6935597915', __FILE__, __LINE__, $db->error());
}

// Бой
$hasfight = '';
if (($dir == 'fight') && (hasfight() || hasproperties($cur_loc['properties'], 'R'))) {
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
		add_death_msg($pun_user['charname'], $pun_user['chargender'], $cur_loc['name']); // Сообщение
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

//	gen_new_rand_enemy(1);
	$db->query('UPDATE '.$db->prefix.'users SET charhp='.$pun_user['charhp'].',chargold='.$pun_user['chargold'].',charexp='.$pun_user['charexp'].' WHERE id='.$pun_user['id']) or error('EN:2390144337', __FILE__, __LINE__, $db->error());	
}

ob_start();

//echo '<h1>Location:</h1>';
//foreach ($loc_list as $cur_loc) {
//	echo pun_htmlspecialchars($cur_loc['name']).'<br/>';
//}

?>

<div id="adminconsole" class="block2col">

	<div class="blockmenu">

		<?php if ($pun_user['is_guest']) { ?>
		
		<?php
		echo '<h2><span>Зал Славы</span></h2>';
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
		echo $p;
		echo '<h2 class="block2"><span>Случайный Герой</span></h2>';
		$result = $db->query('SELECT charname,charrace,charclass,charlevel FROM '.$db->prefix.'users where charlevel>1 order by rand() limit 1') or error('EN:4714503458', __FILE__, __LINE__, $db->error());
		$r = mysqli_fetch_array($result);
		$r['is_guest'] = true;
		echo characterbox($r);
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
		<?php } else 
			echo charmenu('Воскрешение', 'Воскресить', 'game_index.php?dir=revive');
		
		if (hasbuyfood()) echo charmenu('Купить провизию', 'Купить за '.$price_food.' зол.', 'game_index.php?dir=buyfood');		
		if (hasheal()) echo charmenu('Исцелить', $healhp.' ♥ за '.$healhp.' зол.', 'game_index.php?dir=heal');		
		if (hasrest()) echo charmenu('Отдых', 'Разжечь огонь', 'game_index.php?dir=rest');
		if (hasrestininn()) echo charmenu('Снять комнату', 'Снять за '.$price_room.' зол.', 'game_index.php?dir=restininn');
		if (hasfight()) echo charmenu('Атаковать', enemyname($pun_user), 'game_index.php?dir=fight');
		if ($pun_user['is_admmod'])
			generate_game_admin_menu();
		?>

		<?php }; ?>		
</div>

	<?php if ($pun_user['is_guest']) { ?>
	<div class="blockform">
		<h2><span>Здравствуй, путник!</span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Добро пожаловать в Край Серого Дракона!</legend>
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
		</div>
			
		<h2><span>Последние события в мире</span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Последние проишествия в Эльвионе</legend>
					<div class="infldset">
						<ul>
						<?php echo events(); ?>
						</ul>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<?php } else {?>
	<div style="float: right;"><span class="badge badge-pill badge-info"><?php echo '&nbsp;'.charinfo($pun_user).'&nbsp;'; ?></span></div>
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
		<h2><span><?php echo get_fight_msg($hasrestmsglev); ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend><?php echo $pun_user['charenemyname'].' <small>'.enemytype($pun_user['charenemytype']).' '.$pun_user['charenemylevel'].' уровня</small>'; ?></legend>
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
	
	<?php if (ininn()) { ?>
	<div class="blockform">
		<h2><span><?php echo 'Последние события в Эльвионе'; ?></span></h2>
		<div class="box">
			<div class="inform">
				<fieldset>
					<legend>Последние проишествия в мире</legend>
					<div class="infldset">
						<ul><?php echo events(); ?></ul>
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