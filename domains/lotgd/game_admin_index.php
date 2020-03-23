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
require PUN_ROOT.'include/common_admin.php';
require PUN_ROOT.'include/common_game.php';


if (!$pun_user['is_admmod'])
	message($lang_common['No permission'], false, '403 Forbidden');

$action = isset($_GET['action']) ? $_GET['action'] : null;

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['Index']);
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';
?>
<div id="adminconsole" class="block2col">
	<div id="adminmenu" class="blockmenu">
<?php

generate_admin_menu('index');

?>
	</div>
</div>
	<div class="block">
		<h2><span>Статистика LotGD</span></h2>
		<div id="adintro" class="box">
			<div class="inbox">
				<p>Добро пожаловать в панель управления LotGD! Здесь собрана статистика игрового сервера.</p>
			</div>
		</div>

<?php if ($install_file_exists) : ?>
		<h2 class="block2"><span><?php echo $lang_admin_index['Alerts head'] ?></span></h2>
		<div id="adalerts" class="box">
			<p><?php printf($lang_admin_index['Install file exists'], '<a href="admin_index.php?action=remove_install_file">'.$lang_admin_index['Delete install file'].'</a>') ?></p>
		</div>
<?php endif; ?>

		<h2 class="block2"><span>Информация о LotGD</span></h2>
		<div id="adstats" class="box">
			<div class="inbox">
				<dl>
					<dt>Версия LotGD</dt>
					<dd>
						<b><?php echo gameversion(); ?></b> - <a target="_blank" href="https://github.com/devapromix/lotgd">Проект на GitHub</a>
					</dd>
				</dl>
			</div>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

require PUN_ROOT.'footer.php';
