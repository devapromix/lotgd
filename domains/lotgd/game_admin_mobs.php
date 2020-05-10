<?php

/**
 * Copyright (C) 2008-2012 FluxBB
 * based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_admin.php';


if ($pun_user['g_id'] != PUN_ADMIN)
	message($lang_common['No permission'], false, '403 Forbidden');

// Add a mob
if (isset($_POST['add_mob']))
{
	confirm_referrer('game_admin_mobs.php');

	$name = pun_trim($_POST['mname']);
	$type = pun_trim($_POST['mtype']);
	$areal = pun_trim($_POST['mareal']);
	$level = pun_trim($_POST['mlevel']);

	$db->query('INSERT INTO '.$db->prefix.'mobs (name, type, areal, level) VALUES (\''.$db->escape($name).'\', \''.$db->escape($type).'\', \''.$db->escape($areal).'\', \''.$db->escape($level).'\')') or error('Unable to add mob', __FILE__, __LINE__, $db->error());

	redirect('game_admin_mobs.php', 'Моб добавлен!');
}

// Update a mob
else if (isset($_POST['update']))
{
	confirm_referrer('game_admin_mobs.php');
	$id = intval(key($_POST['update']));

	$name = pun_trim($_POST['name'][$id]);
	$type = pun_trim($_POST['type'][$id]);
	$areal = pun_trim($_POST['areal'][$id]);
	$level = pun_trim($_POST['level'][$id]);

	$db->query('UPDATE '.$db->prefix.'mobs SET name=\''.$db->escape($name).'\', type=\''.$db->escape($type).'\', areal=\''.$db->escape($areal).'\', level=\''.$db->escape($level).'\' WHERE id='.$id) or error('Unable to update mob', __FILE__, __LINE__, $db->error());

	redirect('game_admin_mobs.php', 'Параметры моба обновлены!');
}

// Remove a mob
else if (isset($_POST['remove']))
{
	confirm_referrer('game_admin_mobs.php');

	$id = intval(key($_POST['remove']));

	$db->query('DELETE FROM '.$db->prefix.'mobs WHERE id='.$id) or error('Unable to delete mob', __FILE__, __LINE__, $db->error());

	redirect('game_admin_mobs.php',  'Моб удален!');
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], 'Бестиарий');
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_admin_menu('mobs');

function thead(){?>
							<thead>
								<tr>
									<th>Название</th>
									<th>Вид</th>
									<th>Ареал</th>
									<th>Уровень</th>
									<th>Действие</th>
								</tr>
							</thead>
<?php
}

?>
	<div class="blockform">
		<h2><span>Бестиарий</span></h2>
		<div class="box">
			<form id="mobs" method="post" action="game_admin_mobs.php">
				<div class="inform">
					<fieldset>
						<legend>Добавить нового моба</legend>
						<div class="infldset">
							<ul>
							<li><b>Виды мобов:</b>
							<?php
							$r = '';
							for ($i=0;$i<9;$i++) {
								if (enemytype($i) <> '') {
									$s = ($i==0)?' ':', ';
									$r .= $s.enemytype($i).'('.$i.')';
								}
							}
							echo $r.'.';
							?>
							</li>
							</ul>
							<table>
							<?php echo thead(); ?>
							<tbody>
								<tr>
									<td><input type="text" name="mname" size="16" maxlength="60" tabindex="1" /></td>
									<td><input type="text" name="mtype" size="3" maxlength="3" tabindex="2" /></td>
									<td><input type="text" name="mareal" size="3" maxlength="3" tabindex="3" /></td>
									<td><input type="text" name="mlevel" size="3" maxlength="3" tabindex="4" /></td>
									<td width="100%"><input type="submit" name="add_mob" value="Добавить" tabindex="5" /></td>
								</tr>
							</tbody>
							</table>
						</div>
					</fieldset>
				</div>
				
				<div class="inform">
					<fieldset>
						<legend>Список мобов</legend>
						<div class="infldset">
<?php

$result = $db->query('SELECT id, name, type, areal, level FROM '.$db->prefix.'mobs ORDER BY id') or error('Unable to fetch mob list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
{
	echo '<table>';
	echo thead();
	echo '<tbody>';
	while ($cur_mob = $db->fetch_assoc($result))
		echo "\t\t\t\t\t\t\t\t".'<tr><td><input type="text" name="name['.$cur_mob['id'].']" value="'.pun_htmlspecialchars($cur_mob['name']).'" size="16" maxlength="60" /></td><td><input type="text" name="type['.$cur_mob['id'].']" value="'.pun_htmlspecialchars($cur_mob['type']).'" size="3" maxlength="3" /></td><td><input type="text" name="areal['.$cur_mob['id'].']" value="'.pun_htmlspecialchars($cur_mob['areal']).'" size="3" maxlength="3" /></td><td><input type="text" name="level['.$cur_mob['id'].']" value="'.pun_htmlspecialchars($cur_mob['level']).'" size="3" maxlength="3" /></td><td width="100%"><input type="submit" name="update['.$cur_mob['id'].']" value="Изменить" />&#160;<input type="submit" name="remove['.$cur_mob['id'].']" value="Удалить" /></td></tr>'."\n";
	echo '</tbody>';
	echo '</table>';
}

?>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

require PUN_ROOT.'footer.php';
