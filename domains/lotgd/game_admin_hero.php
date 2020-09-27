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

if (isset($_POST['update']))
{
	confirm_referrer('game_admin_hero.php');

	$chargold = pun_trim($_POST['chargold']);
	$charenemy = pun_trim($_POST['charenemy']);

	$db->query('UPDATE '.$db->prefix.'users SET chargold='.$chargold.',charenemy='.$charenemy.' WHERE id='.$pun_user['id']) or error('EN:7928887912', __FILE__, __LINE__, $db->error());

	redirect('game_admin_hero.php', 'Параметры героя обновлены!');
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], 'Герой');
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_admin_menu('hero');
?>

	<div class="blockform">
		<h2><span>Герой</span></h2>
		<div class="box">
			<form id="hero" method="post" action="game_admin_hero.php">

				<div class="inform">
					<fieldset>
						<?php 
							echo charinfo($pun_user);
						?>
					</fieldset>
				</div>

			</form>
		</div>
	</div>
	
	<div class="blockform">
		<h2><span>Инвентарь</span></h2>
		<div class="box">
			<form id="hero" method="post" action="game_admin_hero.php">

				<div class="inform">
					<fieldset>
						<legend>Инвентарь</legend>
						<?php 
							echo '<table>';
							echo '<tr>';
							echo '<td>Золото</td><td><input type="text" name="chargold" value="' . $pun_user['chargold'] . '" size="15" maxlength="15" /></td>';
							echo '</tr>';
							echo '</table>';
						?>
					</fieldset>
				</div>

				<div class="inform">
					<fieldset>
						<legend>Враг</legend>
						<?php 
							echo '<table>';
							echo '<tr>';
							echo '<td>Id врага</td><td><input type="text" name="charenemy" value="' . $pun_user['charenemy'] . '" size="15" maxlength="15" /></td>';
							echo '</tr>';
							echo '</table>';
							echo '<input type="submit" name="update" value="Изменить" />';
						?>
					</fieldset>
				</div>

			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

require PUN_ROOT.'footer.php';

?>
