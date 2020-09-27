<?php

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

//
// Display the admin navigation menu
//
function generate_game_admin_menu($page = '') {
	?>
	<h2 class="block2"><span>Админка LotGD</span></h2>
	<div class="box">
		<div class="inbox">
			<ul>
				<li<?php if ($page == 'stat') echo ' class="isactive"'; ?>><a href="game_admin_index.php">Статистика</a></li>
				<li<?php if ($page == 'mobs') echo ' class="isactive"'; ?>><a href="game_admin_mobs.php">Бестиарий</a></li>
				<li<?php if ($page == 'hero') echo ' class="isactive"'; ?>><a href="game_admin_hero.php">Герой</a></li>
			</ul>
		</div>
	</div>
	<?php
}

?>