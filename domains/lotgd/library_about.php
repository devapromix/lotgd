<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_game.php';
require PUN_ROOT.'include/common_library.php';


$page_title = 'Об игре / Библиотека / Край Серого Дракона';
define('PUN_ACTIVE_PAGE', 'admin');
require PUN_ROOT.'header.php';

generate_library_menu('about');



?>
	<div class="block">
		<h2><span>Об игре</span></h2>
		<div id="adintro" class="box">
			<legend>Версия игры</legend>
			<div class="inbox">
				<p>Текущая версия игры: <?php echo gameversion(); ?></p>
			</div>

		</div>
	</div>
</div>

<?php
require PUN_ROOT.'footer.php';
?>